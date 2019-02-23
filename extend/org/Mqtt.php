<?php
namespace org;

class Mqtt {

    // MQTT control packet types (here left shifted 4 bits)
    const MQTT_CONNECT     = 0x10; // Client request to connect to Server
    const MQTT_CONNACK     = 0x20; // Connect acknowledgment
    const MQTT_PUBLISH     = 0x30; // Publish message
    const MQTT_PUBACK      = 0x40; // Publish acknowledgment
    const MQTT_PUBREC      = 0x50; // Publish received (assured delivery part 1)
    const MQTT_PUBREL      = 0x60; // Publish release (assured delivery part 2)
    const MQTT_PUBCOMP     = 0x70; // Publish complete (assured delivery part 3)
    const MQTT_SUBSCRIBE   = 0x80; // Client subscribe request
    const MQTT_SUBACK      = 0x90; // Subscribe acknowledgment
    const MQTT_UNSUBSCRIBE = 0xa0; // Unsubscribe request
    const MQTT_UNSUBACK    = 0xb0; // Unsubscribe acknowledgment
    const MQTT_PINGREQ     = 0xc0; // PING request
    const MQTT_PINGRESP    = 0xd0; // PING response
    const MQTT_DISCONNECT  = 0xe0; // Client is disconnecting

    // MQTT quality of service levels
    const MQTT_QOS0 = 0x00;
    const MQTT_QOS1 = 0x01;
    const MQTT_QOS2 = 0x02;

    // MQTT status on last read from stream
    const READ_STATUS_NO_READ = 0;
    const READ_STATUS_OK = 200;
    const READ_STATUS_ERROR = 400;
    const READ_STATUS_ERROR_HEADER = 401;
    const READ_STATUS_ERROR_PACKETLENGTH = 402;
    const READ_STATUS_ERROR_PAYLOAD = 403;

    private $socket = null;           // Socket resource reference
    private $socketTimeout;           // Default socket timeout in milliseconds
    private $socketReadDelay = 1000;  // Delay in milliseconds between read attempts on socket

    private $protocol = 'tcp';
    private $serverAddress;
    private $serverPort;
    private $clientId;

    public $caFile = null;
    public $localCert = null;
    public $localPrivateKey = null;

    private $connectCleanSession;
    private $connectKeepAlive;
    private $connectWill = false;
    private $connectWillQos;
    private $connectWillRetain;
    private $connectUsername;
    private $connectPassword;

    private $willTopic;
    private $willMessage;

    private $packetId;         // Packet identifier to validate return packets
    private $pingReqTime;      // Time when last PINGREQ was sent
    private $serverAliveTime;  // Time for last response from server

    private $debug = false;
    private $lastReadStatus = self::READ_STATUS_NO_READ;

    private $packetQueue = []; // Queue for received but unhandled packages

    public $lastConnectResult = 0;

    /**
     * Class constructor - Sets up connection parameters
     *
     * @param string $address Address to server
     * @param string $port Port to server
     * @param string $protocol Which protocol to use
     */
    function __construct($address, $port=null, $protocol='tcp'){
        if ($this->setConnection($address, $port, $protocol)) {
            ;
        }
        $this->packetId = rand(1,100)*100; // Reduce risk of creating duplicate ids in sequential sessions
    }

    /**
     * Class destructor - Close socket
     */
    function __destruct(){
        $this->close();
    }

    /**
     * Setup conection parameters
     *
     * @param string $address
     * @param string $port
     * @param string $protocol
     * @return boolean If return false then using default parameters where validation failed
     */
    function setConnection($address, $port=null, $protocol='tcp'){
        $this->serverAddress = $address;
        $this->serverPort = $port;

        // Validate protocol
        $protocol = strtolower($protocol);
        if (($protocol != 'tcp') && !self::isEncryptionProtocol($protocol)) {
            $this->debugMessage('Invalid protocol ('.$protocol.'). Setting to default (tcp).');
            $this->protocol = 'tcp';
            return false;
        }
        $this->protocol = $protocol;

        return true;
    }

    /**
     * Build url for connecting to stream
     *
     * @return string
     */
    private function getUrl() {
        $url = '';
        if ($this->protocol) $url .= $this->protocol .'://';
        $url .= $this->serverAddress;
        if ($this->serverPort) $url .= ':'. $this->serverPort;
        return $url;
    }

    /**
     * Check if encryption protocol is supported
     *
     * @param string $protcol
     * @return boolean
     */
    private static function isEncryptionProtocol($protocol) {
        return in_array(strtolower($protocol), ['ssl', 'tls', 'tlsv1.0', 'tlsv1.1', 'tlsv1.2', 'sslv3']);
    }

    /**
     * Sets server certificate and protocol for ssl/tls encryption
     *
     * @param string $caFile CA file to identify server
     * @param string $protocl Crypto protocol (See http://php.net/manual/en/migration56.openssl.php)
     * @return boolean False if settings failed, else true
     */
    public function setEncryption($caFile, $protocol = null) {
        if (file_exists($caFile)) {
            $this->caFile = $caFile;
        } else {
            $this->debugMessage('CA file not found');
            return false;
        }
        if(self::isEncryptionProtocol($protocol)) {
            $this->protocol = $protocol;
        } else if (!is_null($protocol)) {
            $this->debugMessage('Unknown encryption protocol');
            return false;
        }
        return true;
    }

    /**
     * Sets client crt and key files for client-side authentication
     *
     * @param string $crtFile Client certificate file
     * @param string $keyFile Client key file
     * @return boolean False if settings failed, else true
     */
    public function setClientEncryption($certificateFile, $keyFile) {
        if (!file_exists($certificateFile)) {
            $this->debugMessage('Client certificate file not found');
            return false;
        }
        if (!file_exists($keyFile)) {
            $this->debugMessage('Client key file not found');
            return false;
        }
        $this->localCert= $certificateFile;
        $this->localPrivateKey = $keyFile;
        return true;
    }

    /**
     * Set authentication details to be used when connecting
     *
     * @param string $username Username
     * @param string $password Password
     */
    public function setAuthentication($username, $password) {
        $this->connectUsername= $username;
        $this->connectPassword = $password;
    }

    /**
     * Set will (last message defined by MQTT) to send when connection is lost
     *
     * @param string $topic
     * @param string $message
     * @param integer $qos
     * @param boolean $retain
     */
    public function setWill($topic, $message, $qos=1, $retain=false) {
        $this->connectWill = true;
        $this->connectWillQos = $qos;
        $this->connectWillRetain = $retain;
        $this->willTopic = $topic;
        $this->willMessage = $message;
    }

    /**
     * Connect to MQTT server
     *
     * @param string $clientId Unique id used by the server to identify the client
     * @param boolean $cleanSession Set true to clear session on server, ie queued messages are purged (not recieved)
     * @param integer $keepAlive Number of seconds a connection is considered to be alive without traffic
     * @param integer $timeout Number of millliseconds before timeout when reading from socket
     * @return boolean Returns false if connection failed
     */
    public function sendConnect($clientId, $cleanSession=false, $keepAlive=10, $timeout=5000) {
        if (!$this->serverAddress) return false;

        // Basic validation of clientid
        // Note: A MQTT server may accept other chars and more than 23 chars in the clientid but that is optional,
        // all chars below up to 23 chars are required to be accepted (see section "3.1.3.1 Client Identifier" of the standard)
        if(preg_match("/[^0-9a-zA-Z]/",$clientId)) {
            $this->debugMessage('ClientId can only contain characters 0-9,a-z,A-Z');
            return false;
        }
        if(strlen($clientId) > 23) {
            $this->debugMessage('ClientId max length is 23 characters/numbers');
            return false;
        }
        $this->clientId = $clientId;

        $this->connectCleanSession = $cleanSession;
        $this->connectKeepAlive = $keepAlive;
        $this->socketTimeout = $timeout;

        // Setup certificates if encryption protocol selected
        if ($this->isEncryptionProtocol($this->protocol)) {
            $mozillaCiphers = implode(':', array(
                'ECDHE-RSA-AES128-GCM-SHA256',
                'ECDHE-ECDSA-AES128-GCM-SHA256',
                'ECDHE-RSA-AES256-GCM-SHA384',
                'ECDHE-ECDSA-AES256-GCM-SHA384',
                'DHE-RSA-AES128-GCM-SHA256',
                'DHE-DSS-AES128-GCM-SHA256',
                'kEDH+AESGCM',
                'ECDHE-RSA-AES128-SHA256',
                'ECDHE-ECDSA-AES128-SHA256',
                'ECDHE-RSA-AES128-SHA',
                'ECDHE-ECDSA-AES128-SHA',
                'ECDHE-RSA-AES256-SHA384',
                'ECDHE-ECDSA-AES256-SHA384',
                'ECDHE-RSA-AES256-SHA',
                'ECDHE-ECDSA-AES256-SHA',
                'DHE-RSA-AES128-SHA256',
                'DHE-RSA-AES128-SHA',
                'DHE-DSS-AES128-SHA256',
                'DHE-RSA-AES256-SHA256',
                'DHE-DSS-AES256-SHA',
                'DHE-RSA-AES256-SHA',
                'AES128-GCM-SHA256',
                'AES256-GCM-SHA384',
                'ECDHE-RSA-RC4-SHA',
                'ECDHE-ECDSA-RC4-SHA',
                'AES128',
                'AES256',
                'RC4-SHA',
                'HIGH',
                '!aNULL',
                '!eNULL',
                '!EXPORT',
                '!DES',
                '!3DES',
                '!MD5',
                '!PSK'
            ));
            // Secure socket communication with these parameters, a ca-file is required
            $options = [];
            $options['verify_peer'] = true;
            $options['verify_peer_name'] = true;
            $options['verify_depth'] = 5;
            $options['disable_compression'] = true;
            $options['SNI_enabled'] = true;
            $options['ciphers'] = $mozillaCiphers;
            if($this->caFile) {
                $options['cafile'] = $this->caFile;
            }
            if($this->localCert) {
                $options['local_cert'] = $this->localCert;
                if ($this->localPrivateKey) {
                    $options['local_pk'] = $this->localPrivateKey;
                }
            }
            $socketContext = stream_context_create(['ssl' => $options]);
            $this->debugMessage('Settings socket options: '. var_export($options, true));
        } else {
            $socketContext = null;
        }

        // Try to open socket
        try {
            $this->debugMessage('Opening socket to: '. $this->getUrl());
            if ($socketContext) {
                $this->socket = stream_socket_client($this->getUrl(), $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $socketContext);
            } else {
                $this->socket = stream_socket_client($this->getUrl(), $errno, $errstr, 10, STREAM_CLIENT_CONNECT);
            }
        } catch (\ErrorException $error) {
            $this->debugMessage('Exception: Could not open stream with error message: '. $error->getMessage());
            $this->socket = null;
            return false;
        }

        // Check if socket was opened successfully
        if ($this->socket === false) {
            $this->socket = null;
            $this->debugMessage('Connection failed. Error-no:'. $errno .' Error message: '. $errstr);
            return false;
        }

        // Set socket timeout
        ini_set('default_socket_timeout', 10);
        stream_set_timeout($this->socket, 0, $this->socketTimeout * 1000);
        // Set stream to non-blocking mode, ie do not wait to read if stream is empty
        stream_set_blocking($this->socket, true);

        // Calculate connect flags to use in CONNECT header
        $connectFlags = 0;
        if ($this->connectCleanSession) $connectFlags += 0x02;
        if ($this->connectWill) {
            $connectFlags += 0x04;
            if ($this->connectWillQos) $connectFlags += ($this->connectWill << 3);
            if ($this->connectWillRetain) $connectFlags += 0x20;
        }
        if ($this->connectUsername) {
            $connectFlags += 0x80;
            if ($this->connectPassword) $connectFlags += 0x40;
        }

        // Build payload and header for CONNECT-packet
        $payload = chr(0x00).chr(0x04);   // MSB & LSB length of MQTT = 4
        $payload .= 'MQTT';
        $payload .= chr(0x04);            // Protocol level (3.1.1)
        $payload .= chr($connectFlags);   // Connect flags
        $payload .= chr($this->connectKeepAlive >> 8);     // Keepalive (MSB)
        $payload .= chr($this->connectKeepAlive & 0xff);   // Keepalive (LSB)
        if ($this->connectCleanSession && empty($this->clientId)) {
            $this->clientId = rand(1,999999999);
        }
        if ($this->clientId) {
            $payload .= $this->createPayload($this->clientId);
        }
        if($this->connectWill){
            $payload .= $this->createPayload($this->willTopic);
            $payload .= $this->createPayload($this->willMessage);
        }
        if($this->connectUsername) {
            $payload .= $this->createPayload($this->connectUsername);
        }
        if ($this->connectPassword) {
            $payload .= $this->createPayload($this->connectPassword);
        }
        $header = $this->createHeader(self::MQTT_CONNECT, $payload);
        $this->debugMessage('Sending CONNECT');
        $this->send($header . $payload);

        // Wait for CONNACK packet
        $response = $this->waitForPacket(self::MQTT_CONNACK);
        if($response !== false && ($response[2] == chr(0))) {
            $this->debugMessage('Connected to MQTT');
            $this->lastConnectResult = 0;
            return true;
        } else {
            $this->debugMessage('Connection failed! Error: '. ord($response[2]));
            $this->lastConnectResult = ord($response[2]);
            $this->close();
            return false;
        }
    }

    /**
     * Publish a topic and message (QoS 0,1,2 supported)
     *
     * @param string $topic
     * @param string $message
     * @param byte $qos
     * @return boolean
     */
    public function sendPublish($topic, $message, $qos = self::MQTT_QOS1) {
        if(!$this->isConnected()) return false;

        if($qos!=self::MQTT_QOS0 && $qos!=self::MQTT_QOS1 && $qos!=self::MQTT_QOS2) return false;

        $packetId = $this->getNextPacketId();
        $payload = $this->createPayload($topic);
        if($qos >= self::MQTT_QOS1) {
            // Packet identifier required for QoS level >= 1
            $payload .= $this->getPacketIdPayload();
        }
        $payload .= $message;

        $dupFlag = 0;
        $retain = 0;
        $header = $this->createHeader(self::MQTT_PUBLISH + ($dupFlag<<3) + ($qos<<1) + $retain, $payload);
        $this->debugMessage('Sending PUBLISH');
        $this->send($header . $payload);

        if($qos == self::MQTT_QOS1) {
            // If QoS level 1, only a PUBACK packet is expected
            $response = $this->waitForPacket(self::MQTT_PUBACK, $packetId);
            if($response === false) {
                $this->debugMessage('Packet missing, expecting PUBACK');
                return false;
            }
        } elseif($qos == self::MQTT_QOS2) {
            // If QoS level 2, a PUBREC packet is expected
            $response = $this->waitForPacket(self::MQTT_PUBREC, $packetId);
            if($response === false) {
                $this->debugMessage('Packet missing, expecting PUBREC');
                return false;
            }

            // Send PUBREL
            $this->sendPubRel($response['packetId']);

            // A PUBCOMP packet is expected
            $response = $this->waitForPacket(self::MQTT_PUBCOMP, $packetId);
            if($response === false) {
                $this->debugMessage('Packet missing, expecting PUBCOMP');
                return false;
            }
        }

        return true;
    }

    /**
     * Send PUBACK as response to a recieved PUBLISH packet (QoS Level 1)
     *
     * @param integer $packetId Packet identifier of PUBLISH packet
     * @return boolean Returns true if packet sent successfully
     */
    public function sendPubAck($packetId) {
        if(!$this->isConnected()) return false;

        $payload = chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
        $header = $this->createHeader(self::MQTT_PUBACK, $payload);
        $this->debugMessage('Sending PUBACK');
        $this->send($header . $payload);

        return true;
    }

    /**
     * Send PUBREC as response to a recieved PUBLISH packet (QoS Level 2)
     *
     * @param integer $packetId Packet identifier of PUBLISH packet
     * @return boolean Returns true if packet sent successfully
     */
    public function sendPubRec($packetId) {
        if(!$this->isConnected()) return false;

        $payload = chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
        $header = $this->createHeader(self::MQTT_PUBREC, $payload);
        $this->debugMessage('Sending PUBREC');
        $this->send($header . $payload);

        return true;
    }

    /**
     * Send PUBREL as response to a recieved PUBREC packet (QoS Level 2)
     *
     * @param integer $packetId Packet identifier of PUBLISH packet
     * @return boolean Returns true if packet sent successfully
     */
    public function sendPubRel($packetId) {
        if(!$this->isConnected()) return false;

        $payload = chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
        $header = $this->createHeader(self::MQTT_PUBREL, $payload);
        $this->debugMessage('Sending PUBREL');
        $this->send($header . $payload);

        return true;
    }

    /**
     * Send PUBCOMP as response to a recieved PUBREL packet (QoS Level 2)
     *
     * @param integer $packetId Packet identifier of PUBLISH packet
     * @return boolean Returns true if packet sent successfully
     */
    public function sendPubComp($packetId) {
        if(!$this->isConnected()) return false;

        $payload = chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
        $header = $this->createHeader(self::MQTT_PUBCOMP, $payload);
        $this->debugMessage('Sending PUBCOMP');
        $this->send($header . $payload);

        return true;
    }

    /**
     * Subscribe to topics with a quality of service
     *
     * @param string[] $topics Topics to subscribe for
     * @param integer $qos Quality of serivce for all topics
     * @return boolean Returns true if SUBACK was recieved
     */
    public function sendSubscribe($topics, $qos = self::MQTT_QOS1) {
        if (!is_array($topics)) $topics = [$topics];
        if(!$this->isConnected()) return false;

        $packetId = $this->getNextPacketId();
        $payload = $this->getPacketIdPayload();
        foreach($topics as $topic) {
            $payload .= $this->createPayload($topic);
            $payload .= chr($qos);
        }
        $header = $this->createHeader(self::MQTT_SUBSCRIBE + 0x02, $payload);
        $this->debugMessage('Sending SUBSCRIBE');
        $this->send($header . $payload);

        // A SUBACK packet is expected
        $response = $this->waitForPacket(self::MQTT_SUBACK, $packetId);
        if($response === false) {
            $this->debugMessage('Packet missing, expecting SUBACK');
            return false;
        }
        $responsePayload = substr($response, 3);   // skip header and identifier (3 bytes)
        if (strlen($responsePayload) != count($topics)) {
            $this->debugMessage('Did not recieve SUBACK for all topics');
            return false;
        }

        // Check which subscriptions that were approved
        $topicsResult = [];
        $i = 0;
        foreach ($topics as $topic) {
            $topicsResult[$topic] = [];
            if ($responsePayload[$i] > 0x02) {
                $topicsResult[$topic]['success'] = false;
                $$topicsResult[$topic]['qosGiven'] = null;
            } else {
                $topicsResult[$topic]['success'] = true;
                $topicsResult[$topic]['qosGiven'] = (int) ord($responsePayload[$i]);
            }
            $i++;
        }

        return $topicsResult;
    }

    /**
     * Send unsubscribe packet for given topics
     *
     * @param string[] $topics
     * @return boolean Returns true if UNSUBACK was recieved
     */
    public function sendUnsubscribe($topics) {
        if(!$this->isConnected()) return false;

        $packetId = $this->getNextPacketId();
        $payload = $this->getPacketIdPayload();
        foreach($topics as $topic) {
            $payload .= $this->createPayload($topic);
        }
        $header = $this->createHeader(self::MQTT_UNSUBSCRIBE + 0x02, $payload);
        $this->debugMessage('Sending UNSUBSCRIBE');
        $this->send($header . $payload);

        // An UNSUBACK packet is expected
        $response = $this->waitForPacket(self::MQTT_UNSUBACK, $packetId);
        if($response === false) {
            $this->debugMessage('Invalid packet received, expecting UNSUBACK');
            return false;
        }
        return true;
    }

    /**
     * Sends PINGREQ packet to server
     *
     * @return boolean Returns true if PINGRESP was recieved
     */
    public function sendPing() {
        if(!$this->isConnected()) return false;

        $this->timeSincePingReq = time();
        $header = $this->createHeader(self::MQTT_PINGREQ);
        $this->debugMessage('Sending PING');
        $this->send($header);
        $this->pingReqTime = time();

        // A PINGRESP packet is expected
        $response = waitForPacket(self::MQTT_PINGRESP);
        if($responseHeader === false) {
            $this->debugMessage('Invalid packet received, expecting PINGRESP');
            return false;
        }

        return true;
    }

    /**
     * Send disconnect and close socket
     */
    public function sendDisconnect() {
        if($this->isConnected()) {
            $header = $this->createHeader(self::MQTT_DISCONNECT);
            $this->debugMessage('Sending DISCONNECT');
            $this->send($header);
            $this->close();
        }
    }

    /**
     * Close socket
     */
    public function close() {
        if($this->isConnected()) {
            $this->debugMessage('Closing socket');
            stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
            $this->socket = null;
            $this->serverAliveTime = null;
        }
    }

    /**
     * Check if connected to stream
     * @return boolean
     */
    public function isConnected() {
        return !empty($this->socket);
    }

    /**
     * Check if connection is alive
     * @return boolean
     */
    public function isAlive() {
        return $this->isConnected() && ($this->serverAliveTime + $this->connectKeepAlive <= time());
    }

    /**
     * Set debug mode, if true then output progress messages
     *
     * @param boolean $mode
     */
    public function setDebug($mode = true) {
        $this->debug = $mode;
    }

    /**
     * Print message to console if debug mode is on
     *
     * @param string $message
     */
    private function debugMessage($message) {
        if ($this->debug) {
            echo 'MQTT: '. $message .PHP_EOL;
        }
    }

    /**
     * Return next packet identifier to use in MQTT packet
     * Max 2 bytes to be used, restart on 0 if end reached
     *
     * @return integer
     */
    private function getNextPacketId() {
        return ($this->packetId = ($this->packetId + 1) & 0xffff);
    }

    /**
     * Return payload of packet id, use latest generated packet id as default
     *
     * @param integer $packetId
     * @return string Two chars with apyload to add in MQTT-message
     */
    private function getPacketIdPayload($packetId = null) {
        if (empty($packetId)) $packetId = $this->packetId;
        return chr(($packetId & 0xff00)>>8) . chr($packetId & 0xff);
    }

    /**
     * Add payload length as bytes to begining of string and return
     *
     * @param string $payload
     * @return string
     */
    private function createPayload($payload) {
        $fullLength = strlen($payload);
        $retval = chr($fullLength>>8).chr($fullLength&0xff).$payload;
        return $retval;
    }

    /**
     * Decode payload using inital length (2 bytes) and return as string array
     *
     * @param string $payload
     * @return string[]
     */
    private function decodePayload($payload) {
        $result = [];
        while (strlen($payload) >= 2) {
            $length = ord($payload[0])<<8 + ord($payload[1]);
            if (strlen($payload) <= $length + 2) {
                $result[] = substr($payload, 2, $length);
            }
            $payload = substr($payload, min($length + 2, strlen($payload)));
        }
        return $result;
    }

    /**
     * Send data to open socket
     *
     * @param string $data
     * @return boolean Only returns true if all data was sent
     */
    private function send($data) {
        if ($this->socket) {
            $result = fwrite($this->socket, $data);
            if (($result !== false) && ($result == strlen($data))) {
                $this->serverAliveTime = time();
                return true;
            }
        }
        return false;
    }

    /**
     * Read bytes from socket until x bytes read, eof reached or socket timeout
     *
     * @param int $bytes Number of bytes to read
     * @return string Return bytes read as a string
     */
    private function readBytes($bytes) {
        if (!$this->socket) return false;
        //if ($bytes == 0) return '';
        $bytesLeft = $bytes;
        $result = '';
        do {
            // If stream at end, close down socket and exit
            if(feof($this->socket)) {
                $this->debugMessage('Reached EOF for stream');
                $this->close();
                return $result;
            }
            // Try to read from stream
            $str = fread($this->socket, $bytesLeft);
            if ($str !== false && strlen($str) > 0) {
                $result .= $str;
                $bytesLeft -= strlen($str);
            }
            if ($bytesLeft <= 0) {
                // If all bytes read, then return them
                $this->serverAliveTime = time();
                return $result;
            }
            // Check if timeout
            $info = stream_get_meta_data($this->socket);
            if ($info['timed_out']) {
                $this->debugMessage('Read timeout');
                return false;
            }
            // Wait a while before trying to read again (in micro seconds)
            //sleep(1);
           usleep($this->socketReadDelay * 1000);
        } while (true);
    }

    /**
     * Encode length to bytes to send in stream
     *
     * @param integer $len
     * @return string
     */
    private function encodeLength($len) {
        if ($len <= 0 || $len >= 128*128*128*128) {
            // illegal length
            return false;
        }
        $output = '';
        do {
            $byte = $len & 0x7f;  // keep lowest 7 bits
            $len = $len >> 7;     // shift away lowest 7 bits
            if ($len > 0) {
                $byte = $byte | 0x80; // set high bit to indicate continuation
            }
            $output .= chr($byte);
        } while ($len > 0);
        return $output;
    }

    /**
     * Return length of packet by reading from stream
     *
     * @return integer
     */
    private function readPacketLength() {
        $bytesRead = 0;
        $len = 0;
        $multiplier = 1;
        do {
            if ($bytesRead > 4) {
                return false; // Malformed length
            }
            $str = $this->readBytes(1);
            if ($str === false || strlen($str) != 1) {
                return false; // Unexpected end of stream
            }
            $byte = ord($str[0]);
            $len += ($byte & 0x7f) * $multiplier;
            $isContinued = ($byte & 0x80);
            if ($isContinued) {
                $multiplier *= 128;
            }
            $bytesRead++;
        } while ($isContinued);
        return $len;
    }

    /**
     * Create MQTT header from command and payload
     *
     * @param int $command Command to send
     * @param string $payload Payload to be sent
     *
     * @return string Header to send
     */
    private function createHeader($command, $payload = '') {
        return chr($command) . $this->encodeLength(strlen($payload));
    }

    /**
     * Read next packet from stream
     *
     * @return boolean
     */
    private function readNextPacket() {
        do {
            $header = $this->readBytes(1);
            if ($header === false) {
                $this->lastReadStatus = self::READ_STATUS_ERROR_HEADER;
                return false;
            }
        } while ((ord($header)&0xf0) == 0);     // 0 is illegal control code to start with

        $packetLength = $this->readPacketLength();
        if ($packetLength === false) {
            $this->debugMessage('Could not decode packet length');
            $this->lastReadStatus = self::READ_STATUS_ERROR_PACKETLENGTH;
            return false;
        }

        $payload = $this->readBytes($packetLength);
        if ($payload === false) {
            $this->lastReadStatus = self::READ_STATUS_ERROR_PAYLOAD;
            return false;
        }
        $this->debugMessage('Packet response: '. self::str2hex($header . $payload));
        $this->lastReadStatus = self::READ_STATUS_OK;
        return $header . $payload;
    }

    public function getLastReadStatus() {
        return $this->lastReadStatus;
    }

    public function hasMoreToRead() {
        return ($this->lastReadStatus == self::READ_STATUS_OK) && $this->isConnected();
    }

    /**
     * Read packets from stream and save to queue. Quit after x packets or timeout.
     *
     * @param integer $maxPackets Packet id the message must match
     * @return integer Number of packets read
     */
    private function readPackets($maxPackets = 100) {
        $receivedPackets = 0;
        while (($packet = $this->readNextPacket()) !== false && ($receivedPackets < $maxPackets)) {
            $this->packetQueue[] = $packet;
            $receivedPackets++;
        }
        return $receivedPackets;
    }

    /**
     * Wait until a certain packet is found in the stream.
     * Save other recieved packets in queue.
     *
     * @param byte $header Header to look for (only 4 high bits) 0xf0
     * @param integer $verifyPacketId Packet id the message must match
     * @return boolean
     */
    private function waitForPacket($header, $verifyPacketId = false) {
        // first check unhandled packets
        foreach ($this->packetQueue as $key => $packet) {
            if ($this->isPacketVerified($packet, $header, $verifyPacketId)) {
                // if found, remove from queue and return packet
                unset($this->packetQueue[$key]);
                return $packet;
            }
        }
        // if not found in queue, start reading from stream until found or timeout
        do {
            $packet = $this->readNextPacket();
            if ($packet === false || empty($packet)) return false;
            if ($this->isPacketVerified($packet, $header, $verifyPacketId)) {
                return $packet;
            }
            // another packet found, save it to queue
            $this->packetQueue[] = $packet;
        } while(true);
    }

    /**
     * Check if packet is of a given type and packet id match latest sent packet id
     *
     * @param string $packet
     * @param char $header
     * @param integer $verifyPacketId
     * @return boolean
     */
    private function isPacketVerified($packet, $header, $verifyPacketId = false) {
        if (is_string($packet) && strlen($packet) >= 1) {
            if ((int)(ord($packet[0])&0xf0) == (int)$header) {
                if ($verifyPacketId === false) return true;
                if (strlen($packet) >= 3) {
                    $receivedPacketId = (int)(ord($packet[1])<<8) + ord($packet[2]);
                    if($verifyPacketId == $receivedPacketId) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get packets matching a header from the queue and remove from queue
     *
     * @param char $header
     * @return string[]
     */
    public function getQueuePackets($header) {
        $foundPackets = [];
        foreach ($this->packetQueue as $key => $packet) {
            if ($this->isPacketVerified($packet, $header)) {
                $foundPackets[] = $packet;
                unset($this->packetQueue[$key]);
            }
        }
        return $foundPackets;
    }

    /**
     * Get PUBLISH packets and return them as messages
     *
     * @param integer $maxMessages Max messages to read
     * @param boolean $sendPubAck If true, then send PUBACK to MQTT-server
     * @return string[] All PUBLISH messages
     */
    public function getPublishMessages($maxMessages = 100, $sendPubAck = false) {
        $packetsRead = $this->readPackets($maxMessages); //$maxMessages);
        $packets = $this->getQueuePackets(self::MQTT_PUBLISH);
        $messages = [];
        foreach ($packets as $key => $packet) {
            $message = $this->decodePublish($packet);
            if ($message) {
                $messages[] = $message;
                if ($sendPubAck && ($message['qos'] == self::MQTT_QOS1)) {
                    $this->sendPubAck($message['packetId']);
                }
            }
        }
        return $messages;
    }

    /**
     * Decode a publish packet to its attributes
     *
     * @param string $packet
     * @return array|boolean Return message or false if decode failed
     */
    public function decodePublish($packet) {
        if (!is_string($packet) || (strlen($packet) <= 3)) {
            return false;
        }
        $flags = ord($packet[0]) & 0x0f;
        $duplicate = ($flags == 0x80);
        $retain = ($flags == 0x01);
        $qos = ($flags>>1) & 0x03;
        $topicLength = (ord($packet[1])<<8) + ord($packet[2]);
        $topic = substr($packet, 3, $topicLength);

        $payload = substr($packet, 3 + $topicLength);   // Get the payload of the packet
        if ($qos == 0) {
            // no packet id for QoS 0, the payload is the message
            $message = $payload;
            $packetId = NULL;
        } else {
            if (strlen($payload) >= 2) {
                $packetId = (ord($payload[0])<<8) + ord($payload[1]);
                $message = substr($payload, 2);   // skip packet id (2 bytes) for QoS 1 and 2
            } else {
                // 2 byte packet id required, but not found. exit gracefully (no failure)
                $packetId = NULL;
                $message = '';
            }
        }
        return [
            'topic' => self::convertActiveMqTopic($topic),
            'message' => $message,
            'retain' => $retain,
            'duplicate' => $duplicate,
            'qos' => $qos,
            'packetId' => $packetId,
        ];
    }

    /**
     * Replace ActiveMQ special characters to MQTT-standard
     *
     * @param string $topic
     * @return string
     */
    private static function convertActiveMqTopic($topic) {
        $topic = str_replace(".","/", $topic);
        $topic = str_replace("*","+", $topic);
        $topic = str_replace(">","#", $topic);
        return $topic;
    }

    /**
     * Return a string interpreted as hex and ASCII (between 0x20-0x7f)
     * Good for displaying recieved packets
     *
     * @param string $str
     * @return string
     */
    private function str2hex($str) {
        $hex = '';
        $ascii = '';
        for ($i=0; $i<strlen($str); $i++) {
            $char = $str[$i];
            if (ord($char) >= 0x20 && ord($char) <= 0x7f) {
                $ascii .= $char;
            } else {
                $ascii .= '.';
            }
            $hex .= dechex(ord($char)).' ';
        }
        return $hex . '"'. $ascii .'"';
    }

    public function dumpQueue() {
        foreach ($this->packetQueue as $packet) {
            $this->str2hex($packet) . PHP_EOL;
        }
    }
}

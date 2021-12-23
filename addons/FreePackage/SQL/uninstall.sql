alter table jshop_order_items drop column is_free;

DROP TABLE `jshop_free_package`;

delete from jshop_hooks where name='addgoodfreepackage';
delete from jshop_hooks where name='addgoodsafterfreepackage';
delete from jshop_hooks where name='editgoodsafterfreepackage';
delete from jshop_hooks where name='editgoodsfreepackage';
delete from jshop_hooks where name='orderpayedafter';

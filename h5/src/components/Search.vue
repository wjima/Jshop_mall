<template>
    <div class="search">
        <router-link to="/searchpage">
            <div class="search-input">
                <form action="" method="post">
                    <i class="search-icon"></i>
                    <input type="text" value="" placeholder="搜索商品"/>
                </form>
            </div>
        </router-link>
    </div>
</template>

<style type="text/css">
	.search .search-input form{
        width: 100%;
        height: 1rem;
        padding: .2rem;
        position: relative;
    }
</style>

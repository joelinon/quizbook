<?php 
$username = htmlspecialchars($_SESSION['username']); #för användarnamnet
?>
<style>
@import url("styles.css");
</style>
<nav>
    <a href="index.php"><div class='logo'>Quiz<span>Book</span></div></a>
    <div class="spc"></div>
    <div class='user'>
        <span class='normal'><strong><?= $username ?? " " ?></strong></span>
        <a href="logout.php">Logga ut</a>
    </div>
</nav>

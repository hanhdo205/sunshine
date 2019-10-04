<ul class="menu" style="margin-top: 58px;">
    <li><a class="<?php echo (($page=="cay-he-thong")?"current":"");?>" href="/cay-he-thong.html">Hệ thống</a></li>
    <li><a class="<?php echo (($page=="bang-pha-he")?"current":"");?>" href="/bang-pha-he.html">Xem cây ngang</a></li>
    <li><a class="<?php echo (($page=="bang-ke-hoa-hong")?"current":"");?>" href="/bang-ke-hoa-hong.html">Hoa hồng</a></li>
    <li><a class="<?php echo (($page=="ho-so-ca-nhan" || $page=="account")?"current":"");?>" href="/ho-so-ca-nhan.html">Hồ sơ cá nhân</a></li>
    <li><a class="<?php echo (($page=="doi-mat-khau" || $page=="change-password")?"current":"");?>" href="/doi-mat-khau.html">Đổi mât khẩu</a></li>
    <li><a href="<?php echo md5("signout".date("dmH"))?>">Thoát</a></li>
</ul>


<?
//$_SESSION['u_type'] == '1' == "Root Admin"
if(display_1($_DISPLAY[1]=='Administration')){?>
<div class="analyticsSubBar">
<ul class="navSubBarTabs" style="display: block;">
<li class="<?if(display_2($_DISPLAY[2])=='Dashboard'){?> active<?}?>"><a data-lang="DASHBOARD" href="/admin/dashboard/">Dashboard</a></li>
<?if($_SESSION['u_type'] == '1'){?><li class="<?if(display_2($_DISPLAY[2])=='Root Administrator'){?> active<?}?>"><a data-lang="ROOT" href="/admin/root/">Root Admin</a></li><?}?>
<?if($_SESSION['u_type'] == '1'){?><li class="<?if(display_2($_DISPLAY[2])=='Property Administrators'){?> active<?}?>"><a data-lang="PROPERTYADMINS" href="/admin/property-admins/">Property Admins</a></li><?}?>
<li class="<?if(display_2($_DISPLAY[2])=='Property Details'){?> active<?}?>"><a data-lang="PROPERTY" href="/admin/property/">Property Details</a></li>
<li class="<?if(display_2($_DISPLAY[2])=='Rates'){?> active<?}?>"><a data-lang="RATES" href="/admin/rates/">Rates</a></li>
<li class="<?if(display_2($_DISPLAY[2])=='Rooms'){?> active<?}?>"><a data-lang="ROOMS" href="/admin/rooms/">Rooms</a></li>
<li class="<?if(display_2($_DISPLAY[2])=='Services'){?> active<?}?>"><a data-lang="SERVICES" href="/admin/services/">Services</a></li>
<li class="<?if(display_2($_DISPLAY[2])=='System Users'){?> active<?}?>"><a data-lang="USERS" href="/admin/users/">Users</a></li>
<li class="<?if(display_2($_DISPLAY[2])=='POS'){?> active<?}?>"><a data-lang="POS" href="/admin/pos/">POS</a></li>
</ul></div>
<?if(display_2($_DISPLAY[2])=='Property Details'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>property/">Contact Details</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>property/floors/">Floors</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}elseif(display_2($_DISPLAY[2])=='POS'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>pos/">POS Points</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>pos/categories/">Categories</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>pos/products/">Products</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}elseif(display_2($_DISPLAY[2])=='Rates'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rates/">Rate Classes</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rates/addons/">Rent Add-ons</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rates/taxes/">Taxes</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}elseif(display_2($_DISPLAY[2])=='Rooms'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rooms/amenities/">Room Amenities</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rooms/classes/">Room Classes</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rooms/types/">Room Types</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>rooms/">Rooms</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}elseif(display_2($_DISPLAY[2])=='Services'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>">Departments</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>">Service</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>">Staff</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>">Resources</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>">Special</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}elseif(display_2($_DISPLAY[2])=='System Users'){?>
<div class="filterBox">
<ul class="filtersList">
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>users/">Users</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>users/levels/">User Levels</a></li>
<li><a style="text-align: center;" class="filterButton" href="<?=$_CFG['rootWebAdmin']?>users/shifts/">Shifts</a></li>
</ul>
<div style="clear: both;"></div>
</div>
<?}?>

<?}?>


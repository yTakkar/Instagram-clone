<div class="pro_nav inst grp_nav <?php if($groups->isGrpAdmin($grp, $session) == false){echo "pri_grp_nav";} ?>">
  <ul>
    <li><a href="grp_posts" data-hint="grp_posts" data-src="ask" class="inst_grp_nav">Posts</a></li>
    <li><a href="grp_members" data-hint="grp_members" data-src="ask" class="inst_grp_nav">Members</a></li>
    <li><a href="grp_photos" data-hint="grp_photos" data-src="ask" class="inst_grp_nav">Photos</a></li>
    <li><a href="grp_videos" data-hint="grp_videos" data-src="ask" class="inst_grp_nav">Videos</a></li>
    <?php if($groups->isGrpAdmin($grp, $session)){ ?>
    <li><a href="grp_edit" data-hint="grp_edit" data-src="ask" class="inst_grp_nav">Edit</a></li>
    <li><a href="grp_add_members" data-hint="grp_add_members" data-src="ask" class="inst_grp_nav">Add members</a></li>
    <?php } ?>
    <li><a href="grp_about" data-hint="grp_about" data-src="ask" class="inst_grp_nav">About</a></li>
  </ul>
</div>

<!-- pro_nav_active -->

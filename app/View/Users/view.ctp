<?php echo $this->Session->flash(); ?>
<div class="page-header">
    <h1>Users</h1>
</div>
<ul class="media-list">
	<li>
		<ul>
		<li class="media">
			<?php debug($user); ?>
		</li>
    	<li class="media">
    		<?php echo $user['User']['id']; ?>
    	</li>
    	<li class="media">
    		<?php echo $user['User']['username']; ?>
    	</li>
    	<li class="media">
    		
    	</li>
    	</ul>
    </li>
</ul>
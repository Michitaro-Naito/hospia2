<!-- Simple user index for testing. -->
<div class="page-header">
    <h1>Users</h1>
</div>
<ul class="media-list">
<?php foreach ($users as $user): ?>
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
<?php endforeach; ?>
<?php unset($user); ?>
</ul>
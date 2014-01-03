<h2><?php echo $typeName; ?> Log</h2>
<h3><?php echo $logFile['name']; ?></h3>

<?php
echo $this->element('log_file_content');
?>

<?php if (count($logFiles) > 1): ?>
<h5>All Logs</h5>
<table class="table">
<tr>
	<th>Filename</th>
	<th>Size</th>
	<th>Modified</th>
	<th>Created</th>
</tr>
<?php foreach ($logFiles as $logFileRow): ?>
	<tr <?php echo $logFileRow['name'] == $logFile['name'] ? 'class="active"' : ''; ?>>
		<td><?php echo $this->Html->link($logFileRow['name'], array('action' => 'view', $logFileRow['name'])); ?></td>
		<td><?php echo $this->Number->toReadableSize($logFileRow['filesize']); ?></td>
		<td><?php echo date('F j, Y H:i:s', $logFileRow['modified']); ?></td>
		<td><?php echo date('F j, Y H:i:s', $logFileRow['created']); ?></td>
	</tr>
<?php endforeach; ?>
</table>
<?php endif;?>
<h1>Log Files</h1>
<table class="table">
<tr>
	<th>Log Type</th>
	<th># Logs</th>
	<th>Last Modified</th>
</tr>
<?php foreach ($logFileTypes as $logFileType): ?>
<tr>
	<td><?php echo $this->Html->link($logFileType['type'], array('action' => 'view', 'type' => $logFileType['type'])); ?></td>
	<td><?php echo number_format($logFileType['count']); ?></td>
	<td><?php echo date('F j, Y H:i:s', $logFileType['modified']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php
set_time_limit(7200);
//krsort($lines);

$nav = '';
$url = array(0 => $logName, 'base' => false);
$lastPage = $totalPages - 1;
$ids = array();

if (!empty($totalPages) && $totalPages > 1) {
	$padding = 3;
	for ($i = 0; $i <= $lastPage; $i++) {
		if ($i >= ($currentPage - $padding) && $i <= ($currentPage + $padding)) {
			$nav .= $this->Html->tag('li', 
				$this->Html->link($i + 1, Router::url(array('page' => $i) + $url)),
				array('class' => ($i == $currentPage ? 'current disabled' : null))
			);
		}
	}
	$before = $this->Html->tag('li',
			$this->Html->link('<<', Router::url(array('page' => 0) + $url))
		) .
		$this->Html->tag('li', 
			$this->Html->link('<', Router::url(array('page' => $currentPage > 0 ? $currentPage - 1 : 0) + $url))
		);
	$after = $this->Html->tag('li',
			$this->Html->link('>', Router::url(array('page' => $currentPage < $lastPage ? $currentPage + 1 : $lastPage) + $url))
		) . 
		$this->Html->tag('li',
			$this->Html->link('>>', Router::url(array('page' => $lastPage) + $url))
		);
	
	$nav = $this->Html->div('pagination pagination-centered', $this->Html->tag('ul', $before . $nav . $after));
	echo sprintf('Showing %d-%d results of %d total', $currentPage * $perPage, ($currentPage + 1) * ($perPage) - 1, $lineCount);
}

echo $nav;
?>
<dl id="logfilecontent">
<?php foreach ($logFileContent as $k => $line): 
	$stamp = strtotime($line['date']);
	$date = date('m/d/y H:i', $stamp);
	
	$id = 'logfilecontent-line' . $k;
	
	$hover = '';
	$more = array();
	if (!empty($line['url'])) {
		$hover = $line['url'];
		$more['URL'] = sprintf('<a target="_blank" href="%s">%s</a>', $line['url'], $line['url']);
	}
	if (!empty($line['stack'])) {
		$more['Stack'] = '<ul><li>'. implode('</li><li>', $line['stack']) . '</li></ul>';
	}
	?>
	<dt>
		<?php 
		if (!empty($more)) {
			echo $this->Html->link($date, '#', array(
				'class' => 'logfilecontent-more-toggle', 
				'title' => $hover,
				'data-more-toggle-target' => $id,
			));
		} else {
			echo $this->Html->tag('strong', $date);
		}
		?>
	</dt>
	<dd><?php 
		echo $line['text']; 
		if (!empty($more)): ?>
			<div class="logfilecontent-more" id="<?php echo $id; ?>">
				<dl><?php
				foreach ($more as $key => $val) {
					echo $this->Html->tag('dt', $key);
					echo $this->Html->tag('dd', $val);
				}
				?></dl>
			</div><?php 
		endif; ?>
	</dd>
<?php endforeach; ?>
</dl>
<?php echo $nav; ?>

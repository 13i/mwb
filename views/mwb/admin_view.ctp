<div class="mwb view">
	<h2>MWB : <?php echo $mwbFile['name'] ?></h2>
	<dl>
		<dt><?php __d('mwb', 'Name'); ?></dt>
			<dd><?php echo $mwbFile['name'] ?>&nbsp;</dd>
		<dt class="altrow"><?php __d('mwb', 'Path'); ?></dt>
			<dd class="altrow"><?php echo str_replace(APP, '', $mwbFile['path']); ?>&nbsp;</dd>
		<dt><?php __d('mwb', 'Schema Path'); ?></dt>
			<dd><?php echo str_replace(APP, '', $mwbFile['schema_path']); ?>&nbsp;</dd>
		<dt class="altrow"><?php __d('mwb', 'Generated'); ?></dt>
			<dd class="altrow"><?php echo $mwbFile['status'] > -1 ? __d('mwb', 'Yes', true) : __d('mwb', 'No', true); ?>&nbsp;</dd>
		<dt><?php __d('mwb', 'Up to date'); ?></dt>
			<dd><?php echo $mwbFile['status'] == 0 ? __d('mwb', 'Yes', true) : __d('mwb', 'No', true); ?>&nbsp;</dd>
	</dl>
	<?php foreach($mwbFile['tables'] as $name => $columns): ?>
		<div style="margin-top:50px;">
			<h3><?php echo Inflector::humanize($name); ?></h3>
			<table cellpadding="0" cellspacing="0">
				<colgroup style="width:40%;"></colgroup>
				<colgroup style="width:15%;"></colgroup>
				<colgroup style="width:15%;"></colgroup>
				<colgroup style="width:15%;"></colgroup>
				<colgroup style="width:15%;"></colgroup>
				<tr>
					<th><?php __d('mwb', 'Column'); ?></th>
					<th><?php __d('mwb', 'Type'); ?></th>
					<th><?php __d('mwb', 'Length'); ?></th>
					<th><?php __d('mwb', 'Null'); ?></th>
					<th><?php __d('mwb', 'Primary'); ?></th>
				</tr>
				<?php
				$i = 0; 
				foreach($columns as $_name => $column):
					$class = $i++%2 ? null : ' class="altrow"'; 
				?>
					<tr<?php echo $class; ?>>
						<td><strong><?php echo $_name; ?></strong>&nbsp;</td>
						<td><?php echo @$column['type']; ?>&nbsp;</td>
						<td><?php echo @$column['length']; ?>&nbsp;</td>
						<td><?php echo @$column['null']; ?>&nbsp;</td>
						<td><?php echo @$column['primary']; ?>&nbsp;</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	<?php endforeach; ?>
</div>
<div class="actions">
	<h3><?php __d('mwb', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('mwb', 'List MWB files', true), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__d('mwb', 'Generate schema', true), array('action' => 'generate', $mwbFile['name'])); ?></li>
	</ul>
</div>
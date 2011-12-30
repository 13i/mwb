<div class="mwb index">
	<h2>MWB</h2>
	<p><?php __d('mwb', 'This plugin allows to generate schema files from <abbr title="MySQL Workbench">MWB</abbr> files.'); ?></p>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php __d('mwb', 'Name'); ?></th>
			<th class="actions"><?php __d('mwb', 'Actions'); ?></th>
		</tr>
		<?php foreach($mwbFiles as $name => $path): ?>
			<tr>
				<td><?php echo $name; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link(__d('mwb', 'View', true), array('action' => 'view', $name)); ?> 
					<?php echo $this->Html->link(__d('mwb', 'Generate', true), array('action' => 'generate', $name)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<div class="actions">
	
</div>
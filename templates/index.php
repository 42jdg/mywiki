<?php
// ToDo: Must be a better way to include this
\OCP\Util::addStyle('mywiki', 'fontawesome/css/all.min');

\OCP\Util::addScript('mywiki', 'easy-markdown-editor-master/dist/easymde.min');
\OCP\Util::addStyle('mywiki', '../js/easy-markdown-editor-master/dist/easymde.min');

\OCP\Util::addScript('mywiki', 'WikiDropdownHelper');
\OCP\Util::addScript('mywiki', 'WikiNavigation');
\OCP\Util::addScript('mywiki', 'WikiContent');
\OCP\Util::addScript('mywiki', 'WikiPages');
\OCP\Util::addScript('mywiki', 'script');
\OCP\Util::addStyle('mywiki', 'style');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('content/index')); ?>
		</div>
	</div>
</div>


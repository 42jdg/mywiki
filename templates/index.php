<?php
\OCP\Util::addScript('mywiki', 'WikiDropdownHelper');
\OCP\Util::addScript('mywiki', 'WikiNavigation');
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


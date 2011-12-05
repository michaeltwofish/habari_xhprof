<?php
class XHProf extends Plugin
{
	private $run;

	public function alias()
	{
		return array('template_footer' => array('action_admin_footer', 'action_template_footer'));
	}

	public function action_plugins_loaded()
	{
		if ( extension_loaded('xhprof') ) {
			$dir = dirname($this->get_file());
			include $dir.'/lib/utils/xhprof_lib.php';
			include $dir.'/lib/utils/xhprof_runs.php';
			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
		}
	}

	public function template_footer($theme)
	{
		if ( extension_loaded('xhprof') && User::identify()->loggedin ) {
			$profiler_namespace = Options::get('title'); // namespace for your application
			$xhprof_data = xhprof_disable();
			$xhprof_runs = new XHProfRuns_Default();
			$run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace);
			// url to the XHProf UI libraries (change the host name and path)
			$profiler_url = sprintf('%s/html/index.php?run=%s&source=%s', $this->get_url(), $run_id, $profiler_namespace);
			echo '<a href="'.$profiler_url.'" style="width:80px; padding:2px; background:#c00; text-align:center; position:fixed; bottom:0; right:0; font-size:11px; z-index:999; color:white; display:block;">XHProfiler</a>';

		}
	}
}
?>

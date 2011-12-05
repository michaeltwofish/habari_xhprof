<?php
class XHProf extends Plugin
{
	/**
	 * add ACL tokens when this plugin is activated
	 */
	public function action_plugin_activation( $file )
	{
		ACL::create_token( 'XHProf', 'Access profiling data', 'xhprof' );
	}

	/**
	 * remove ACL tokens when this plugin is deactivated
	 */
	function action_plugin_deactivation( $plugin_file )
	{
		ACL::destroy_token( 'XHProf' );
	}

	/**
	 * filter the permissions so that admin users can use this plugin
	 */
	public function filter_admin_access_tokens( $require_any, $page, $type )
	{
		// we only need to filter if the Page request is for our page
		if ( 'xhprof' == $page ) {
			// we can safely clobber any existing $require_any
			// passed because our page didn't match anything in
			// the adminhandler case statement
			$require_any = array( 'super_user' => true, 'xhprof' => true );
		}
		return $require_any;
	}

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
			// This would link to the admin/xhprof page
			//$profiler_url = sprintf('%s/xhprof?run=%s&source=%s', Site::get_url('admin'), $run_id, $profiler_namespace);
			$profiler_url = sprintf('%s/html/index.php?run=%s&source=%s', $this->get_url(), $run_id, $profiler_namespace);
			echo '<a href="'.$profiler_url.'" style="width:80px; padding:2px; background:#c00; text-align:center; position:fixed; bottom:0; right:0; font-size:11px; z-index:999; color:white; display:block;">XHProfiler</a>';

		}
	}

	public function action_init()
	{
		$this->add_template('xhprof', dirname(__FILE__) . '/templates/xhprof.php');
	}

	public function action_admin_theme_get_xhprof( AdminHandler $handler, Theme $theme )
	{
		$run = $handler->handler_vars['run'];
		$source = $handler->handler_vars['source'];

		//$theme->report = jeez, I don't know the bloody xhprof output stuff is a mess
		$theme->report = 'put your report here';
	}

	public function action_admin_header( $theme ) {
		if ( $theme->admin_page == 'xhprof' ) {
			//Stack::add('admin_stylesheet', array($this->get_url() . '/xhprof.css', 'screen'));
		}
	}

}
?>

<?php
/*
Plugin Name: Cron Tasks Viewer
Description: Lists all scheduled cron tasks
Version: 1.0.2
Requires at least: 3.2
Author: Roy Orbison, Dr. Max V.
Author URI: https://profiles.wordpress.org/lev0/
Licence: GPLv2 or later
*/

##########################################################################
#                                                                        #
# Original Copyright (C) 2015  Dr. Max V.                                #
# Modifications Copyright (C) 2018  Roy Orbison                          #
#                                                                        #
# This program is free software: you can redistribute it and/or modify   #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# This program is distributed in the hope that it will be useful,        #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with this program.  If not, see <https://www.gnu.org/licenses/>. #
#                                                                        #
##########################################################################

class ViewCronRedux {

	private $load_handle_prefix = 'view-cron-redux';
	private $plugin_name;

	public function __construct() {
		$this->plugin_name = __('Cron Tasks', 'cron-task-viewer-redux');
		if (is_admin()) {
			add_action('admin_menu', array($this, 'addToolsMenu'));
		}
	}

	public function addToolsMenu() {
		add_submenu_page(
			'tools.php'
			, esc_html($this->plugin_name)
			, esc_html($this->plugin_name)
			, 'manage_options'
			, $this->load_handle_prefix
			, array($this, 'showCronTasks')
		);
	}

	private function sortSchedules($a, $b) {
		if ($a['interval'] == $b['interval']) {
			return strcmp($a['display'], $b['display']);
		}
		if ($a['interval']) {
			if ($b['interval']) {
				return $a['interval'] - $b['interval'];
			}
			return -1;
		}
		if ($b['interval']) {
			return 1;
		}
		return 0;
	}

	public function showCronTasks() {
		$cron = _get_cron_array();
		$schedules = wp_get_schedules();
		uasort($schedules, array($this, 'sortSchedules'));
		?>
		<h1><?php echo esc_html($this->plugin_name); ?></h1>
		<h2><?php esc_html_e('Scheduled events', 'cron-task-viewer-redux'); ?></h2>
		<table class="widefat striped" style="width:auto">
			<thead>
				<tr>
					<th><?php esc_html_e('Next Schedule', 'cron-task-viewer-redux'); ?></th>
					<th><?php esc_html_e('Frequency', 'cron-task-viewer-redux'); ?></th>
					<th><?php esc_html_e('Hook', 'cron-task-viewer-redux'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cron as $timestamp => $cronhooks) {
					foreach ($cronhooks as $hook => $events) {
						foreach ($events as $event) { ?>
							<tr>
								<td><?php echo date('Y-m-d H:i:s', $timestamp); ?></td>
								<td><?php echo $event['schedule'] ?
									esc_html($schedules[$event['schedule']]['display']) :
									esc_html__('One-off event', 'cron-task-viewer-redux'); ?></td>
								<td><code><?php echo esc_html($hook); ?></code></td>
							</tr>
						<?php }
					}
				} ?>
			</tbody>
		</table>
		<h2><?php esc_html_e('Available schedules', 'cron-task-viewer-redux'); ?></h2>
		<table class="widefat striped" style="width:auto">
			<thead>
				<tr>
					<th><?php esc_html_e('Frequency', 'cron-task-viewer-redux'); ?></th>
					<th><?php esc_html_e('ID', 'cron-task-viewer-redux'); ?></th>
					<th><?php esc_html_e('Interval (seconds)', 'cron-task-viewer-redux'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($schedules as $schedule_id => $schedule) { ?>
					<tr>
						<td><?php echo esc_html($schedule['display']); ?></td>
						<td><code><?php echo esc_html($schedule_id); ?></code></td>
						<td><?php echo esc_html($schedule['interval']); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}
}

new ViewCronRedux;

const path = require('path');
const { execFile } = require('child_process');

const SCRIPT = path.join(__dirname, '..', 'scripts', 'wp-bootstrap-exec.sh');
const WP_TOOL = path.join(__dirname, '..', 'wp-tool', 'index.php');

/**
 * Calls into the live WordPress install's own PHP context (via wp-load.php)
 * to perform setup/teardown and DB-level assertions using WP's real APIs
 * (wp_insert_user, update_user_meta, wc_get_order, ...) instead of driving
 * the browser or hand-rolling SQL. See wp-tool/index.php for the action list.
 */
function wpTool(action, args = {}) {
	return new Promise((resolve, reject) => {
		execFile(
			SCRIPT,
			[WP_TOOL, action, JSON.stringify(args)],
			{ maxBuffer: 10 * 1024 * 1024 },
			(error, stdout, stderr) => {
				if (error) {
					reject(new Error(`wpTool(${action}) failed: ${stderr || error.message}`));
					return;
				}
				const line = stdout.trim().split('\n').pop();
				try {
					resolve(line ? JSON.parse(line) : null);
				} catch (e) {
					reject(new Error(`wpTool(${action}) returned non-JSON output: ${stdout}`));
				}
			}
		);
	});
}

module.exports = { wpTool };

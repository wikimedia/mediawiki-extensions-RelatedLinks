<?php

if( !defined( 'MEDIAWIKI' ) )
	die( 'Not an entry point.' );

class SpecialRelatedLinks extends SpecialPage {
	public function __construct() {
		parent::__construct( 'RelatedLinks','editinterface');
	}

	public function execute($par) {
		$wgRequest = $this -> getRequest();
		$wgOutput = $this -> getOutput();
		$wgUser = $this -> getUser();
		
		$wgOutput->addModules('ext.relatedLinks');
		$this -> setHeaders();

		if (in_array("sysop", $wgUser -> mGroups) == false) {
			$wgOutput -> addHTML(wfMessage('please_login'));
			return false;
		}

		$param = $wgRequest -> getText('title');
		$links_id = str_replace('Special:RelatedLinks/', '', $param);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);

			switch($_POST['submit_type']) {
				case 'INSERT' :
					$new_url = filter_var($_POST['new_url'], FILTER_VALIDATE_URL);
					$return_url = SkinTemplate::makeSpecialUrl('RelatedLinks') . '/' . $id;

					$dbw = wfGetDB(DB_MASTER);

					if ($dbw -> tableExists('related_links')) {
						$tbl_links = $dbw -> tableName('related_links');
					} else {
						$result = initiateSettingsForRelatedLinks();http://localhost:20010/index.php?title=%ED%8A%B9%EC%88%98%3A%EC%97%B0%EA%B4%80%EC%82%AC%EC%9D%B4%ED%8A%B8/Main_Page
						if (!$result) {
							die('Failed to create table Related Links');
						}
						$tbl_links = $dbw -> tableName('related_links');
					}

					ob_start();
					echo '<strong>' . $id . '</strong>';
					$output = ob_get_contents();
					ob_clean();

					// insert new related site
					$order = $dbw -> selectField($tbl_links, 'MAX( links_order )', array('user_id' => '', 'links_id' => $id));
					$order = ($order) ? $order + 1 : 1;
					$data = array('links_order' => $order, 'links_subject' => $_POST['new_subject'], 'links_url' => $new_url, 'links_enable' => 'true', 'links_datetime' => date("Y-m-d H:i:s"), 'links_id' => $id);
					$result = $dbw -> insert($tbl_links, $data);

					$output .= '<h4>Inserted into sidebar</h4>' . '<ul><li>' . $_POST['new_subject'] . ' (' . $new_url . ')</li></ul>';

					// return link
					$output .= '<br /><a href="' . $return_url . '">Return Page</a>';

					break;
				case 'DELETE' :
					$return_url = SkinTemplate::makeSpecialUrl('RelatedLinks') . ($id != 'Related Links' ? '/' . $id : '');

					$dbw = wfGetDB(DB_MASTER);
					$tbl_links = $dbw -> tableName('related_links');

					ob_start();
					echo '<strong>' . $id . '</strong>';

					$output = ob_get_contents();
					ob_clean();

					// delete sidebar
					if ($_POST['check']) {
						$result = $dbw -> delete($tbl_links, array('id' => $_POST['check']));

						$output .= '<h4>Deleted sidebar</h4>';
					}

					// return link
					$output .= '<br /><a href="' . $return_url . '">Return  Page</a>';
					break;
				default :
					$return_url = SkinTemplate::makeSpecialUrl('RelatedLinks') . '/' . $id;

					$dbw = wfGetDB(DB_MASTER);
					$tbl_links = $dbw -> tableName('related_links');

					ob_start();
					echo '<strong>' . $id . '</strong>';
					$output = ob_get_contents();
					ob_clean();

					// update sidebars
					$output .= '<h4>Updated Sidebar</h4>' . '<ul>';

					foreach ($_POST['order'] as $endex => $order) {
						$data = array('links_order' => $order, 'links_subject' => $_POST['subject'][$endex], 'links_url' => $_POST['url'][$endex], 'links_enable' => ($_POST['enable'][$endex] == 'true' ? 'true' : 'false'));
						$dbw -> update($tbl_links, $data, array('id' => $endex));
						$output .= '<li>' . $_POST['subject'][$endex] . ' (' . $_POST['url'][$endex] . ')' . ($_POST['enable'][$endex] == 'true' ? ' : enabled' : ' : disabled') . '</li>';
					}
					$output .= '</ul>';

					// return link
					$output .= '<br /><a href="' . $return_url . '">Return relatedlinks Page</a>';
			}

		} else {
			$dbr = wfGetDB(DB_SLAVE);

			if ($dbr -> tableExists('related_links')) {
				$tbl_links = $dbr -> tableName('related_links');
			} else {
				$result = initiateSettingsForRelatedLinks();
				if (!$result) {
					die('Failed to create table Related Links');
				}
				$tbl_links = $dbr -> tableName('related_links');
			}

			$fields = array('id', 'links_order', 'links_subject', 'links_url', 'links_enable');
			$conds = array('user_id' => '', 'links_id' => $links_id);
			$opts = array('ORDER BY' => 'links_order');
			$result = $dbr -> select($tbl_links, $fields, $conds, 'Database::select', $opts);
			$rows = $dbr -> numRows($result);

			ob_start();
			?>
					<form id="related_links_form" method="post" action="">
						<fieldset>
							<legend><a href="/index.php/<?=$links_id ?>"><?=$links_id ?></a></legend>
							<?php if ( !$rows ): ?>
							<h4><?php echo wfMessage( 'no_data' ) ?></h4>
							<?php endif ?>
							<input type="hidden" id="submit_type" name="submit_type" value="">
							<input type="hidden" id="submit_id" name="id" value="<?=$links_id ?>">
							<table class="lieTable" style="width: 100%;">
							<colgroup style="width:10%;">
							<colgroup style="width:10%">
							<colgroup style="width:15%">
							<colgroup>
							<colgroup style="width:10%">
							<thead>
							<tr>
								<th><input type="checkbox" id="checkall" /></th>
								<th><?php echo wfMessage('no') ?></th>
								<th><?php echo wfMessage('subject') ?></th>
								<th><?php echo wfMessage('rel_url') ?></th>
								<th><?php echo wfMessage('enable') ?></th>
							<tr>
							</thead>
							<tbody id="links_list">
							<?php while ( $row = $dbr->fetchObject( $result ) ) { ?>
							<tr>
								<td><input type="checkbox" class="checkitem" name="check[<?=$row -> id ?>]" value="<?=$row -> id ?>"></td>
								<td><input type="number" name="order[<?=$row -> id ?>]" value="<?=$row -> links_order ?>" min="1" class="s_order"></td>
								<td><input type="text" name="subject[<?=$row -> id ?>]" value="<?=$row -> links_subject ?>" style="width: 92%;"></td>
								<td><input type="url" name="url[<?=$row -> id ?>]" value="<?=$row -> links_url ?>" style="width: 92%;"></td>
								<td><input type="checkbox" name="enable[<?=$row -> id ?>]" value="true"<?=($row -> links_enable == 'true' ? ' checked' : '') ?>></td>
							</tr>
							<?php } $dbr->freeResult( $result ); ?>
							</tbody>
							<tfoot>
							<tr>
								<th colspan="2"><?php echo wfMessage( 'add_site' ) ?></th>
								<td><input type="text" id="new_subject" name="new_subject" value="" style="width: 92%;"></td>
								<td><input type="text" id="new_url" name="new_url" value="" style="width: 92%;"></td>
								<td><input type="button" id="btn_insert-sidebar" value="<?php echo wfMessage( 'insert' ) ?>"></td>
							</tr>
							</tfoot>
							</table>
							<input type="submit" value="<?php echo wfMessage('modify_all') ?>">
							<input type="button" id="btn_delete-sidebar" value="<?php echo wfMessage('delete') ?>">
						</fieldset>
					</form>
				<?php
				$output = ob_get_contents();
				ob_clean();
				}
				$wgOutput -> addHTML($output);
				}

				}
					
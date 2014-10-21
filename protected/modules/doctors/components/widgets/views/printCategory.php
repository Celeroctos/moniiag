<!-- Look though all categories -->
<? foreach($category as $key => $child) : ?>

	<!-- Skip if child havn't element -->
	<? if (!isset ($child['element'])) {
		continue;
	} else {
		$elementNumber++;
	} ?>

	<!-- If child is category, then print it recursively -->
	<? if ($child['element']['element_id'] == -1) { ?>
		<div style="margin-left:20px;">
		<strong style="text-decoration: underline">
			<? echo $child['element']['name']; ?>
		</strong>
		<p class ="print-elements">
			<? CWidget::createWidget('application.modules.doctors.components.widgets.printCategory', array(
				'categoryToPrint' => $child
			))->run(); ?>
		</p>
		</div>
	<? } else { ?>

		<!-- Skip print, if elements hasn't value -->
		<? if (($element = $child['element']) == null || !($element['value'] != ' ' && $element['value'] != null)) {
			continue;
		} ?>

		<!-- If element's type is 4 (table), then print table -->
		<? if ($element['type'] == '4') {

			// Get table's configure
			$configOfTable =  $element['config'];

			print "<br>";

			// Set this flag to true, if
			// you want to render table
			// with empty values in rows
			$isRenderEmptyTable = false;

			// Decoded json response from element
			$valuesArr = CJSON::decode($element['value']);

			// We will print row's title only if we will
			// have one row in table (cuz we havn't count of
			// elements in json result, Why?)
			$isTableTitleRendered = false;

			// Look though all elements in result
			// and print it
			for($i = 0; $i < $configOfTable['numRows']; $i++) {

				// Skip not set rows
				if(isset($configOfTable['rows'][$i]) && !$isRenderEmptyTable) {
					continue;
				}

				// We will print column's title only if count
				// of columns in table greater then 0
				$isRowTitleRendered = false;

				// Look though all elements in config
				// table with row's title and it's values
				for ($j = 0; $j < $configOfTable['numCols']; $j++) :

					// Get value from array or
					// declare it as null
					$value = !isset($valuesArr[$i][$j]) ? null
						: $valuesArr[$i][$j];

					// If fetched value is null, then don't
					// render it
					if ($value == null && !$isRenderEmptyTable) {
						continue;
					}

					// Render table's header (cuz we havn't
					// count of rows and columns in config
					// table, again why?)
					if (!$isTableTitleRendered && ($isTableTitleRendered = true)) {
						if (isset($configOfTable['cols']) && count($configOfTable['cols']) > 0) : ?>
							<table style="border-collapse: collapse;"><tbody>
							<tr>
								<?php if (isset($configOfTable['rows']) && count($configOfTable['rows']) > 0) : ?>
									<td style="border: 1px solid #000000;">
										<?php echo $configOfTable['rows'][$i]; ?>
									</td>
								<?php endif; ?>
								<?php for ($i = 0; $i < count($configOfTable['cols']); $i++) : ?>
									<td style="border: 1px solid #000000;">
										<?php echo $configOfTable['cols'][$i]; ?>
									</td>
								<?php endfor; ?>
							</tr>
						<?php endif;
					}

					// Print column's title, only we
					// have filled values in that row
					if (!$isRowTitleRendered && ($isRowTitleRendered = true)) : ?>
						<tr>
						<td style="border: 1px solid #000000;">
							<? echo $configOfTable['rows'][$i]; ?>
						</td>
					<?php endif; ?>
					<td style="border: 1px solid #000000;" class="content-<? echo $i . '_' . $j; ?>">
						<? echo $value;?>
					</td>
					</tr>
				<?php endfor;
			}
			if ($isTableTitleRendered) : ?>
				</tbody></table>
			<?php endif;
		} else {
			// If we have data, then swap it
			if ($element['type'] == 6 && $element['value'] != '' && $element['value'] != NULL) {
				if (count(($dateParts = explode('-',$element['value']))) == 3) {
					$element['value'] = $dateParts[2].'.'.$dateParts[1].'.'.$dateParts[0];
				}
			}
			if (($element['is_wrapped'] == '1' && $elementNumber != 1) || ($ignoreBrSettings == true)) {
				print "</br>";
			}
			print $element['label'];
			?> <strong>
				<span class="elementValuePrinting">
					<?php echo $element['value']; ?>
				</span>
			</strong> <?php
			print $element['label_after']; ?>
		<?php
		} ?>
	<? } ?>

<? endforeach; ?>
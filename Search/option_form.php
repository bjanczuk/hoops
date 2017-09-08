<div id="header">
	<div class="header_filter_graph header_chunk" id="filter_header">
		<?php
			if (file_exists('../Filter/')) {
				echo "<a class='search_link' href='../Filter/'><span class='header_link_text'>Filter Players</span></a>";
			}
			else {
				echo "<a class='search_link' href='./Filter/'><span class='header_link_text'>Filter Players</span></a>";
			}
		?>
	</div>

	<div class="header_chunk" id="search_header">
		<div id="searchfield">
		<?php
			if (file_exists('./Search/results.php')) {
				echo '<form action="./Search/results.php" method="get">';
			}
			else {
				echo '<form action="../Search/results.php" method="get">';
			}
		?>
				<input type="text" name="q" class="biginput" id="autocomplete" placeholder="Search for a player or team:">
			</form>
		</div>
	</div>

	<div class="header_filter_graph header_chunk" id="graph_header">
		<?php
			if (file_exists('../Graph/')) {
				echo "<a class='search_link' href='../Graph/'><span class='header_link_text'>Generate Graphs</span></a>";
			}
			else {
				echo "<a class='search_link' href='./Graph/'><span class='header_link_text'>Generate Graphs</span></a>";
			}
		?>
	</div>
</div>
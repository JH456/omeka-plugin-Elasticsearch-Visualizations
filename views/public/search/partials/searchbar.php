<!-- <div class="w3-container w3-cell w3-cell-middle">
  <div class="w3-row">
    <input class="w3-col w3-border-0 l10 w3-input" type="text" placeholder="Search">
    <button class="w3-col l2 w3-button"><i class="fa fa-search"></i></button>
  </div>
</div>

<div class="w3-container w3-cell w3-cell-middle">
  <h3>Start Date</h3>
  <input type="date" name="bday">
</div>

<div class="w3-container w3-cell w3-cell-middle">
  <h3>End Date</h3>
  <input type="date" name="bday">
</div> -->

<customdiv id="elasticsearch-search">
    <form id="elasticsearch-search-form">
        <input type="text" placeholder="Search" title="<?php echo __('Search keywords') ?>" name="q" value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
        <?php foreach($query['facets'] as $facet_name => $facet_values): ?>
            <?php if(is_array($facet_values)): ?>
                <?php foreach($facet_values as $facet_value): ?>
                    <input type="hidden" name="<?php echo "facet_{$facet_name}[]"; ?>" value="<?php echo $facet_value; ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <input type="hidden" name="<?php echo "facet_{$facet_name}"; ?>" value="<?php echo $facet_values; ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="submit" value="Search" />
        <br>
        <a href="javascript:void(0);" id="elasticsearch-help-btn" style="display:block;clear:both; 
                                                                         color:white;
                                                                         font-weight:bold;
                                                                         padding:10px; 
                                                                         padding-left: 20px;">
            <?php echo __("Search Help"); ?>
        </a>
    </form>
</customdiv>

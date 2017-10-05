<?php echo head(array(
    'title' => __('Elasticsearch | Index Items')
)); ?>

<?php echo $this->partial('admin/partials/navigation.php', array('tab' => 'reindex')); ?>

<div id="primary">
    <h2><?php echo __('Index Items'); ?></h2>
    <?php echo flash(); ?>
    <p><?php echo __('Click the button to (re)index the entire site.') ?></p>
    <?php echo $form ?>
    <h2><?php echo __('Job History'); ?></h2>
    <p>The table below shows the indexing processes that have been executed by using the <em>Clear and Reindex</em> button.
        These processes are executed in the background, so you can use this table to check on the status of the indexing process.</p>
    <table>
        <thead>
            <th>Job ID</th>
            <th>User ID</th>
            <th>Status</th>
            <th>Started</th>
            <th>Stopped</th>
        </thead>
    <?php foreach($jobs as $job): ?>
        <tr>
            <td><?php echo $job->id; ?></td>
            <td><?php echo $job->user_id; ?></td>
            <td><?php echo $job->status; ?></td>
            <td><?php echo $job->started; ?></td>
            <td><?php echo $job->stopped; ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>

<?php echo foot(); ?>

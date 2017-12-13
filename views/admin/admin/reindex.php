<?php queue_js_file('elasticsearch'); ?>
<?php echo head(array(
    'title' => __('Elasticsearch | Index Items')
)); ?>

<?php echo $this->partial('admin/partials/navigation.php', array('tab' => 'reindex')); ?>

<div id="primary">
    <h2><?php echo __('Index'); ?></h2>
    <?php echo flash(); ?>
    <p><?php echo __('Click the button to (re)index the entire site.') ?></p>
    <?php echo $form ?>

    <h2><?php echo __('Jobs'); ?></h2>
    <p>The table below shows the most recent indexing jobs that have been executed by using the <em>Clear and Reindex</em> button.
        The jobs are executed in the background, so this table can be used to verify when a job has completed.</p>
    <table>
        <thead>
            <th>Job ID</th>
            <th>User ID</th>
            <th>Status</th>
            <th>Started</th>
            <th>Stopped</th>
        </thead>
    <?php $jobInProgress = false; ?>
    <?php foreach($jobs as $job): ?>
        <?php if(in_array($job->status, array('starting', 'in progress'))) { $jobInProgress = true; } ?>
        <tr class="<?php echo $jobInProgress ? 'jobinprogress' : ''; ?>">
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

<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    <div <?= $options['wrapperAttrs'] ?> >
    <?php endif; ?>
<?php endif; ?>

<?php if ($showLabel && $options['label'] !== false && $options['label_show']): ?>
    <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
<?php endif; ?>

<div class="col-md-10">
    <?php if ($showField): ?>
        <<?= $options['tag'] ?> <?= $options['elemAttrs'] ?>><?= e($options['value']) ?></<?= $options['tag'] ?>>

        <?php include 'help_block.php' ?>

    <?php endif; ?>
</div>


<?php if ($showLabel && $showField): ?>
    <?php if ($options['wrapper'] !== false): ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($showLabel && $showField) : ?>
    <?php if ($options['wrapper'] !== false) : ?>
        <div <?= $options['wrapperAttrs'] ?>>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($showLabel && $options['label'] !== false && $options['label_show']) : ?>
        <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
    <?php endif; ?>

    <div class="col-md-12">
        <?php if ($showField) : ?>
            <?php $emptyVal = $options['empty_value'] ? ['' => $options['empty_value']] : null; ?>
            <?= Form::select($name, (array) $emptyVal + $options['choices'], $options['selected'], $options['attr']) ?>

            <?php if (@$options['small']) : ?>
                <small>
                    <?= $options['small'] ?>
                </small>
            <?php endif; ?>

            <?php if (@$options['error']) : ?>
                <div class="invalid-feedback">
                    <?= $options['error'] ?>
                </div>
            <?php endif; ?>

            <?php include 'help_block.php' ?>

            <?php include 'errors.php' ?>

        <?php endif; ?>
    </div>

    <?php if ($showLabel && $showField) : ?>
        <?php if ($options['wrapper'] !== false) : ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

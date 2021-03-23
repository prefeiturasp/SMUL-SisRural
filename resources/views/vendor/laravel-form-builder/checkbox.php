<?php if ($showLabel && $showField) : ?>
    <?php if ($options['wrapper'] !== false) : ?>
        <div <?= $options['wrapperAttrs'] ?>>
        <?php endif; ?>
    <?php endif; ?>

    <div class="col-md-12 row">
        <div class="ml-3">
            <?php if ($showField) : ?>
                <input type="hidden" name="<?= $name ?>"" value=" 0"> <!-- Fix request->only() Laravel -->
                <?= Form::checkbox($name, $options['value'], $options['checked'], $options['attr']) ?>
            <?php endif; ?>
        </div>

        <?php if ($showLabel && $options['label'] !== false && $options['label_show']) : ?>
            <?= Form::customLabel($name, $options['label'], $options['label_attr']) ?>
        <?php endif; ?>

        <?php if (@$options['small']) : ?>
            <small>
                <?= $options['small'] ?>
            </small>
        <?php endif; ?>

        <?php if (@$options['help_block']) : ?>
            <div class="ml-3">
                <?php include 'help_block.php' ?>
            </div>
        <?php endif; ?>


        <?php include 'errors.php' ?>
    </div>

    <?php if ($showLabel && $showField) : ?>
        <?php if ($options['wrapper'] !== false) : ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

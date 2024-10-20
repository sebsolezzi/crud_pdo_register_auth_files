<?php if ($mensaje): ?>
    <div class="row">
        <div class="col-12 col-md-3 col-lg-3 mx-auto mt-2">
            <div class=" <?php echo $mensaje['tipo']; ?>">
                <p class="text-white text-center p-1"><?php echo $mensaje['mensaje']; ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>
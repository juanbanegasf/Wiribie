<?php
require_once 'includes/functions.php';
?><!-- Agregar vista previa de bio -->
<?php if (!empty($current_user['bio'])): ?>
    <div class="mb-4 p-4 bg-gray-50 rounded-xl">
        <label class="block text-sm font-medium text-gray-700 mb-2">Vista previa actual:</label>
        <div class="text-gray-700 post-content">
            <?php echo linkify($current_user['bio']); ?>
        </div>
    </div>
<?php endif; ?>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Biografía</label>
    <textarea name="bio" 
              rows="4"
              maxlength="300"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none smooth-transition"
              placeholder="Cuéntanos sobre ti... Puedes usar #hashtags, @menciones y enlaces"><?php echo sanitize($current_user['bio'] ?? ''); ?></textarea>
    <p class="text-xs text-gray-500 mt-1">
        Máximo 300 caracteres. Usa 
        <span class="text-sky-500 font-semibold">#hashtags</span>, 
        <span class="text-blue-800 font-semibold">@menciones</span> y 
        <span class="text-blue-600 font-semibold">enlaces</span>
    </p>
</div>
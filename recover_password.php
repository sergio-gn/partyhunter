<?php
/* Template Name: Recover Password */

get_header();

get_template_part( 'parts/navigation' );
?>
<style>
    .container.login {
        min-height: 80vh;
        padding: 2rem 1rem;
    }

    .row {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100%;
    }

    .form_box {
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: 1.5rem;
        display: flex;
        flex-direction: column;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    /* Multi-step registration styles */
    .multi-step-registration {
        width: 100%;
    }

    .registration-step {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .registration-step.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .step-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .step-indicator {
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Photo upload section */
    .photo-upload-section {
        margin-bottom: 2rem;
    }

    .photo-preview-container {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .photo-upload-label {
        cursor: pointer;
        display: block;
    }

    .photo-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px dashed #ddd;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .photo-preview:hover {
        border-color: var(--main_colour, #667eea);
        background: rgba(102, 126, 234, 0.05);
    }

    .photo-preview.has-image {
        border: 3px solid var(--main_colour, #667eea);
        border-style: solid;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .photo-preview svg {
        color: #999;
        margin-bottom: 0.5rem;
    }

    .photo-placeholder-text {
        font-size: 0.85rem;
        color: #666;
        text-align: center;
    }

    .photo-hint {
        text-align: center;
        font-size: 0.9rem;
        color: #666;
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Form inputs */
    .input_label {
        display: flex;
        flex-direction: column;
        margin-bottom: 1.5rem;
    }

    .input_group {
        position: relative;
    }

    .input_group > input,
    .input_group > select {
        border: none;
        border-bottom: 2px solid #e0e0e0;
        width: 100%;
        height: 3rem;
        font-size: 1rem;
        padding-left: 0.875rem;
        padding-right: 0.875rem;
        padding-top: 1.25rem;
        padding-bottom: 0.5rem;
        background: transparent;
        transition: all 0.3s ease;
    }

    .input_group > input:focus {
        outline: none;
        border-bottom-color: var(--main_colour, #667eea);
        background: rgba(102, 126, 234, 0.02);
    }

    .input_group > .omrs-input-label {
        position: absolute;
        top: 1rem;
        left: 0.875rem;
        line-height: 1;
        color: #999;
        transition: all 0.3s ease;
        pointer-events: none;
        font-size: 1rem;
    }

    .input_group > input:focus + .omrs-input-label,
    .input_group > input:valid + .omrs-input-label {
        top: 0.25rem;
        font-size: 0.75rem;
        color: var(--main_colour, #667eea);
    }

    .input_group > input:hover {
        background: rgba(102, 126, 234, 0.02);
    }

    /* Buttons */
    .btn-primary,
    .btn-secondary {
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .form-actions .btn-primary,
    .form-actions .btn-secondary {
        flex: 1;
        margin-top: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form_box {
            padding: 2rem 1.5rem;
            border-radius: 1rem;
        }

        .step-header h2 {
            font-size: 1.5rem;
        }

        .photo-preview {
            width: 100px;
            height: 100px;
        }
    }

    @media (orientation: portrait) {
        .form_box {
            width: 90vw;
            max-width: 500px;
        }
    }
</style>
<section>
    <div class="container login">
        <div class="row">
            <div class="form_box">
                <?php echo do_shortcode('[custom_registration_form]') ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
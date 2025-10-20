<?php
//reusable, project-agnostic functions for rendering basic versions of each input type.
namespace ImagePointe\Utils\Fields;

if (!defined('ABSPATH')) exit;


use function ImagePointe\Utils\Misc\try_get_option_value;


if (!function_exists(__NAMESPACE__ . '\text_field')) {
    function text_field(string $db_setting_name, string $db_option_name, string $id_prefix = 'custom_field')
    {
        $value = try_get_option_value($db_setting_name, $db_option_name);
        $field_name = $db_option_name . '[' . $db_setting_name . ']';
        $field_id = $id_prefix . '_' . $db_setting_name;
?>
        <input type="text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="<?php echo $value; ?>">
    <?php
    }
}

if (!function_exists(__NAMESPACE__ . '\text_area')) {
    function text_area(string $db_setting_name, string $db_option_name, string $id_prefix = 'custom_field', int $rows = 5, int $cols = 50)
    {
        $value = try_get_option_value($db_setting_name, $db_option_name);
        $field_name = $db_option_name . '[' . $db_setting_name . ']';
        $field_id = $id_prefix . '_' . $db_setting_name;

    ?>
        <textarea id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>
    <?php
    }
}

if (!function_exists(__NAMESPACE__ . '\checkbox')) {
    function checkbox(string $db_setting_name, string $db_option_name, string $label, string $id_prefix = 'custom_field')
    {
        $value = try_get_option_value($db_setting_name, $db_option_name);
        $field_name = $db_option_name . '[' . $db_setting_name . ']';
        $field_id = $id_prefix . '_' . $db_setting_name;

    ?>
        <label for="<?php echo $field_id; ?>">
            <input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" value="1" <?php checked(1, $value); ?>>
            <?php echo $label; ?>
        </label>
        <?php
    }
}

if (!class_exists(__NAMESPACE__ . '\RadioButtonConfig')) {
    class RadioButtonConfig
    {
        public function __construct(
            public string $label,
            public string $value
        ) {}
    }
}
if (!function_exists(__NAMESPACE__ . '\radio_buttons')) {
    /**
     * 
     * @param string $db_setting_name The name of this setting.
     * @param string $db_option_name The name of the option row in wp_options.
     * @param RadioButtonConfig[] $buttons The data for each radio button.
     * @param string $id_prefix Optionally specify the prefix for field id attributes.
     */
    function radio_buttons(string $db_setting_name, string $db_option_name, array $buttons, string $id_prefix = 'custom_field')
    {
        $value = try_get_option_value($db_setting_name, $db_option_name);
        $field_name = $db_option_name . '[' . $db_setting_name . ']';

        foreach ($buttons as $button) {
            $this_value = $button->value;
            $field_id = $id_prefix . '_' . $db_setting_name . '_' . $this_value;

        ?>
            <label for="<?php echo $field_id; ?>" style="display:block;">
                <input type="radio" name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" value="<?php echo $this_value; ?>" <?php checked($this_value, $value); ?>>
                <?php echo $button->label; ?>
            </label>
        <?php
        }
    }
}

if (!class_exists(__NAMESPACE__ . '\DropdownOptionConfig')) {
    class DropdownOptionConfig
    {
        public function __construct(
            public string $label,
            public string $value
        ) {}
    }
}
if (!function_exists(__NAMESPACE__ . '\dropdown')) {
    /**
     * 
     * @param string $db_setting_name The name of this setting.
     * @param string $db_option_name The name of the option row in wp_options.
     * @param DropdownOptionConfig[] $options The data for each option.
     * @param string $id_prefix Optionally specify the prefix for field id attributes.
     */
    function dropdown(string $db_setting_name, string $db_option_name, array $options, string $id_prefix = 'custom_field')
    {
        $value = try_get_option_value($db_setting_name, $db_option_name);
        $field_name = $db_option_name . '[' . $db_setting_name . ']';
        $field_id = $id_prefix . '_' . $db_setting_name;

        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>">
            <?php

            foreach ($options as $option) {
                $this_value = $option->value;
            ?>
                <option value="<?php echo $this_value; ?>" <?php selected($this_value, $value); ?>><?php echo $option->label; ?></option>
            <?php
            }
            ?>
        </select>
<?php

    }
}

<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       garidium.com
 * @since      1.0.0
 *
 * @package    g_Ffl_Cockpit
 * @subpackage g_Ffl_Cockpit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    g_Ffl_Cockpit
 * @subpackage g_Ffl_Cockpit/admin
 * @author     Big G <sales@garidium.com>
 */
class g_ffl_Cockpit_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ffl_Cockpit_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ffl_Cockpit_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/g-ffl-cockpit-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('jsoneditor-style', plugin_dir_url(__FILE__) . 'css/jsoneditor.min.css', array(), $this->version, 'all');
        wp_enqueue_style('table-style', plugin_dir_url(__FILE__) . 'css/table.min.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in g_Ffl_Cockpit_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The g_Ffl_Cockpit_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/g-ffl-cockpit-admin.js', array('jquery'), $this->version, true);
        wp_enqueue_script('jsoneditor', plugin_dir_url(__FILE__) . 'js/jsoneditor.min.js');
        wp_enqueue_script('table_component', plugin_dir_url(__FILE__) . 'js/table.min.js');
        wp_enqueue_style('forms');

    }

    public function ffl_load_menu()
    {
        $custom_plugin_name = (get_option('g_ffl_cockpit_plugin_name') != ''? get_option('g_ffl_cockpit_plugin_name') : 'g-FFL Cockpit');
        add_menu_page('g-FFL Cockpit Settings Page', $custom_plugin_name, 'manage_options', 'g-ffl-cockpit-settings', array($this, 'g_ffl_cockpit_settings_page'), 'dashicons-database-import', 70);
        add_action('admin_init', array($this, 'register_g_ffl_cockpit_settings'));
    }

    function register_g_ffl_cockpit_settings()
    {
        //register our settings
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_key');
        //register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_configuration');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_name');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_logo_url');
    }

    function g_ffl_cockpit_settings_page()
    {
        $gFFLCockpitKey = get_option('g_ffl_cockpit_key');
        echo '<script type="text/javascript">
                let gFFLCockpitKey = "' . esc_attr($gFFLCockpitKey) . '"
              </script>';
        ?>
        <!-- Add this in your plugin's main file or enqueue it in your theme's functions.php -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        
        
        <style>
            .tab-pane {
                min-height: 500px;
            }
        </style>
        <div class="wrap">
            <img src="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>">
            <br><br>
            <!-- Tab links -->
            <div class="tab" id="cockpit_main_tab_control">
                <button class="tablinks" onclick="openTab(event, 'configuration')" id="defaultOpen">Configuration</button>
                <button class="tablinks" onclick="openTab(event, 'product_feed')">Product Feed</button>
                <button class="tablinks" onclick="openTab(event, 'fulfillment');of_grid.render(document.getElementById('order_fulfillment_table'));">Fulfillment</button>
                <button class="tablinks" onclick="openTab(event, 'logs');log_grid.render(document.getElementById('log_table'));">Logs</button>
                <button class="tablinks" onclick="openTab(event, 'help_center');load_help_videos();">Help Center</button>
            </div>
            <!-- Tab content -->
            <div id="configuration" class="tabcontent">
                <div class="postbox" style="margin-top: 10px;">
                        <span id="unsaved-indicator">There are <span style="color:red;text-decoration: underline;">Unsaved Changes</span> to your configuratiaon. Hit the "Save Changes" button at the bottom of your screen, or refresh the browser page to revert.</span>
                        <span class="validator_view" id="validation-errors"></span>
                        <?php settings_fields('g-ffl-cockpit-settings'); ?>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
                        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                        <script src="https://garidium.s3.amazonaws.com/ffl-api/plugin/cockpit/tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
                                               
                        <script>
                            const statesAndTerritories = [
                                { value: "", text: "Select State/Territory" },
                                { value: "AL", text: "Alabama" },
                                { value: "AK", text: "Alaska" },
                                { value: "AZ", text: "Arizona" },
                                { value: "AR", text: "Arkansas" },
                                { value: "CA", text: "California" },
                                { value: "CO", text: "Colorado" },
                                { value: "CT", text: "Connecticut" },
                                { value: "DE", text: "Delaware" },
                                { value: "FL", text: "Florida" },
                                { value: "GA", text: "Georgia" },
                                { value: "HI", text: "Hawaii" },
                                { value: "ID", text: "Idaho" },
                                { value: "IL", text: "Illinois" },
                                { value: "IN", text: "Indiana" },
                                { value: "IA", text: "Iowa" },
                                { value: "KS", text: "Kansas" },
                                { value: "KY", text: "Kentucky" },
                                { value: "LA", text: "Louisiana" },
                                { value: "ME", text: "Maine" },
                                { value: "MD", text: "Maryland" },
                                { value: "MA", text: "Massachusetts" },
                                { value: "MI", text: "Michigan" },
                                { value: "MN", text: "Minnesota" },
                                { value: "MS", text: "Mississippi" },
                                { value: "MO", text: "Missouri" },
                                { value: "MT", text: "Montana" },
                                { value: "NE", text: "Nebraska" },
                                { value: "NV", text: "Nevada" },
                                { value: "NH", text: "New Hampshire" },
                                { value: "NJ", text: "New Jersey" },
                                { value: "NM", text: "New Mexico" },
                                { value: "NY", text: "New York" },
                                { value: "NC", text: "North Carolina" },
                                { value: "ND", text: "North Dakota" },
                                { value: "OH", text: "Ohio" },
                                { value: "OK", text: "Oklahoma" },
                                { value: "OR", text: "Oregon" },
                                { value: "PA", text: "Pennsylvania" },
                                { value: "RI", text: "Rhode Island" },
                                { value: "SC", text: "South Carolina" },
                                { value: "SD", text: "South Dakota" },
                                { value: "TN", text: "Tennessee" },
                                { value: "TX", text: "Texas" },
                                { value: "UT", text: "Utah" },
                                { value: "VT", text: "Vermont" },
                                { value: "VA", text: "Virginia" },
                                { value: "WA", text: "Washington" },
                                { value: "WV", text: "West Virginia" },
                                { value: "WI", text: "Wisconsin" },
                                { value: "WY", text: "Wyoming" },
                                { value: "DC", text: "District of Columbia" },
                                { value: "AS", text: "American Samoa" },
                                { value: "GU", text: "Guam" },
                                { value: "MP", text: "Northern Mariana Islands" },
                                { value: "PR", text: "Puerto Rico" },
                                { value: "UM", text: "United States Minor Outlying Islands" },
                                { value: "VI", text: "U.S. Virgin Islands" }
                            ];

                            let typingTimer;
                            const doneTypingInterval = 2000;
                            function setupAutoSave() {
                                const autoSaveFields = document.querySelectorAll('[data-autosave="true"]');
                                autoSaveFields.forEach(field => {
                                    if (!tinymce.get(field.id) && !field.dataset.listenerAdded) {
                                        field.addEventListener('input', () => {
                                            clearTimeout(typingTimer);
                                            typingTimer = setTimeout(() => autoSave(field), doneTypingInterval);
                                        });

                                        field.addEventListener('keydown', () => {
                                            clearTimeout(typingTimer);
                                        });

                                        field.addEventListener('blur', () => autoSave(field));

                                        // Mark this field as having listeners added
                                        field.dataset.listenerAdded = 'true';
                                    }
                                });
                            }

                            function refreshEditor(){
                                load_fancy_editor(editor.get());
                                document.getElementById("unsaved-indicator").style.display='none';
                                initialCockpitConfiguration = editor.get();
                            }
                   
                            function autoSave(field) {
                                let value;
                                if (tinymce.get(field.id)) {
                                    // Handle TinyMCE editor
                                    value = tinymce.get(field.id).getContent();
                                } else {
                                    if (field.type === 'checkbox') {
                                        value = field.checked;
                                    }else{
                                        // Handle regular input/textarea
                                        value = field.value;
                                    }
                                    if (field.type == "number"){
                                        if (!isNaN(value) && value.trim() !== "") {
                                            value = parseFloat(value);
                                        }
                                    }
                                }
                                console.log('Auto-saving form data for field:', field.id, value);
                                const cc = editor.get();
                                setConfigValue(cc, field.id, value);
                                editor.set(cc);
                            }

                          
                            function filterOptions(searchTerm, modalListId) {
                                const filter = searchTerm.toLowerCase();
                                const modalList = document.getElementById(modalListId);
                                const containers = Array.from(modalList.getElementsByClassName('checkbox-option'));

                                const matches = [];
                                const nonMatches = [];

                                // Separate matches and non-matches
                                containers.forEach(container => {
                                    const label = container.getElementsByTagName('label')[0];
                                    const txtValue = label.textContent || label.innerText;
                                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                        matches.push(container);
                                    } else {
                                        nonMatches.push(container);
                                    }
                                });

                                // Clear the modal list
                                while (modalList.firstChild) {
                                    modalList.removeChild(modalList.firstChild);
                                }

                                // Append matches first
                                matches.forEach(container => {
                                    modalList.appendChild(container);
                                    container.style.display = ""; // Ensure matches are displayed
                                });

                                // Add a separator line if there are matches and non-matches
                                if (matches.length > 0 && nonMatches.length > 0) {
                                    const separator = document.createElement('hr');
                                    separator.style.borderTop = "2px solid black"; // Set the thickness of the separator
                                    modalList.appendChild(separator);
                                }

                                // Append non-matches
                                nonMatches.forEach(container => {
                                    modalList.appendChild(container);
                                    container.style.display = ""; // Ensure non-matches are displayed
                                });
                                modalList.scrollTop = 0;
                            }

                            async function load_modal_check_option(modal, action, selectedItemsContainerId) {
                                var element_name = "categories";
                                var element_id = "name";
                                var element_description = "name";
                                if (action == "get_product_classes") {
                                    element_name = "product_classes";
                                    element_id = "product_class";
                                    element_description = "description";
                                }else if (action == "get_manufacturer_list") {
                                    element_name = "manufacturers";
                                    element_id = "name";
                                    element_description = "name";
                                } else if (action == "get_category_list") {
                                    element_name = "categories";
                                    element_id = "name";
                                    element_description = "name";
                                }
                                const response = await fetch("https://ffl-api.garidium.com", {
                                    method: "POST",
                                    headers: {
                                        "Accept": "application/json",
                                        "Content-Type": "application/json",
                                        "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>"
                                    },
                                    body: JSON.stringify({ "action": action })
                                });

                                const selectedItemsContainer = document.getElementById(selectedItemsContainerId);
                                const selectedItems = Array.from(selectedItemsContainer.getElementsByClassName('selected-item')).map(item => item.querySelector('span').textContent);
                                

                                const data = await response.json();
                                try {
                                    if (data != null && data[element_name].length > 0) {
                                        for (let i = 0; i < data[element_name].length; i++) {
                                            const item = data[element_name][i];
                                            const checkbox = document.createElement("input");
                                            checkbox.type = "checkbox";
                                            checkbox.value = item[element_id];
                                            checkbox.id = `checkbox_${element_id}_${item[element_id]}`;

                                            if (selectedItems.includes(checkbox.value)){
                                                checkbox.checked =true;
                                            }

                                            const label = document.createElement("label");
                                            label.htmlFor = `checkbox_${element_id}_${item[element_id]}`;
                                            if (action == "get_product_classes"){
                                                label.textContent = item[element_description] + " (" + item[element_id] + ")";
                                            } else {
                                                label.textContent = item[element_description];
                                            }
                            
                                            const container = document.createElement("div");
                                            container.classList.add("checkbox-option");
                                            container.appendChild(checkbox);
                                            container.appendChild(label);

                                            modal.appendChild(container);
                                        }
                                    }
                                } catch (error) {
                                    alert("Problem retrieving options.");
                                }
                            }
                          
                            function openModal(modalId, selectedItemsId) {
                                const modal = document.getElementById(modalId);
                                const modal_options = document.getElementById(modalId + "List");
                                
                                // Create the search div
                                const searchDiv = document.getElementById(modalId + "SearchDiv");
                                
                                if (searchDiv != null){
                                    const div = document.createElement('div');
                                    div.style = "margin-bottom:20px";
                                    searchDiv.replaceChildren();
                                    var searchType = "brand";
                                    if (modalId.startsWith("category")){
                                        searchType = "category";
                                    } else if (modalId.startsWith("ignoreMap")){
                                        searchType = "ignoreMapBrand";
                                    }
                                    const container = document.createElement('span');
                                    container.style = 'margin-left:20px;';
                                    // Static part of the text
                                    const staticText = document.createTextNode('Filter: ');

                                    // Dynamic part of the text, styled as a link
                                    const link = document.createElement('a');
                                    link.id = searchType + 'SearchInput';
                                    link.href = '#'; // Prevent default anchor behavior
                                    link.textContent = 'Enter Search Term';
                                    link.style.textDecoration = 'underline'; // Style to look like a link
                                    link.style.color = 'blue'; // Optional: Color to look like a link

                                    // Add click listener to prompt for search term and update the text
                                    link.addEventListener('click', function(event) {
                                        event.preventDefault(); // Prevent default anchor behavior
                                        const newValue = prompt("Enter Search Term", link.textContent);
                                        if (newValue !== null) { // Check if the user didn't cancel the prompt
                                            link.textContent = newValue;
                                            filterOptions(document.getElementById(searchType + 'SearchInput').textContent, searchType + 'ModalList');
                                        }
                                    });

                                    // Append the static text and link to the container
                                    container.appendChild(staticText);
                                    container.appendChild(link);
                                  
                                    // Append input element to the div
                                    div.appendChild(container);

                                    // Attach keyup event to the input element
                                    /*
                                    input.onkeyup = function() {
                                        filterOptions(searchType + 'SearchInput', searchType + 'ModalList');
                                    };
                                    */
                                    searchDiv.appendChild(div);
                                }

                                if (modalId != "priceBasedMarginModal"){
                                    modal_options.replaceChildren();
                                    var api_function = "get_product_classes";
                                    if (modalId == "brandModal" || modalId == "ignoreMapBrandModal"){
                                        api_function = "get_manufacturer_list";
                                    }else if (modalId == "categoryModal"){
                                        api_function = "get_category_list";
                                    }
                                    load_modal_check_option(modal_options, api_function, selectedItemsId)
                                }

                                
                                modal.style.display = "block";
                               // modal.style.zIndex = zIndex;
                                modal.setAttribute('data-selected-items-id', selectedItemsId);

                              
                            }

                            function closeModal(modalId) {
                                const modal = document.getElementById(modalId);
                                modal.style.display = "none";
                                
                            }

                            function saveSelections(modalId, itemType) {
                                const modal = document.getElementById(modalId);
                                const selectedItemsId = modal.getAttribute('data-selected-items-id');
                                const selectedItemsContainer = document.getElementById(selectedItemsId);
                                selectedItemsContainer.innerHTML = '';

                                const checkboxes = modal.querySelectorAll('input[type="checkbox"]:checked');
                                checkboxes.forEach(checkbox => {

                                    // add item to configuration
                                    addConfigArrayItem(selectedItemsContainer, checkbox.value);

                                    const div = document.createElement('div');
                                    div.className = 'selected-item';
                                    const span = document.createElement('span');
                                    span.textContent = checkbox.value;
                                    const link = document.createElement('i');
                                    link.className = 'fas fa-trash-alt remove-link';
                                    link.title = 'Remove item'; 
                                    link.onclick = () => removeConfigArrayItem(selectedItemsContainer, div);
                                    div.appendChild(span);
                                    div.appendChild(link);
                                    selectedItemsContainer.appendChild(div);
                                });

                                closeModal(modalId);
                            }

                            // Close the modal when the user clicks anywhere outside of it
                            window.onclick = function(event) {
                                const modals = document.querySelectorAll('.modal');
                                modals.forEach(modal => {
                                    if (event.target === modal) {
                                        modal.style.display = "none";
                                    }
                                });
                            };

                            function addSelectedItemsToContainer(selectedItemsContainer, items){
                                for (const item of items) {
                                    const div = document.createElement('div');
                                    div.className = 'selected-item';
                                    const span = document.createElement('span');
                                    span.textContent = item;
                                    const link = document.createElement('i');
                                    link.className = 'fas fa-trash-alt remove-link';
                                    link.title = 'Remove item'; 
                                    link.onclick = () => removeConfigArrayItem(selectedItemsContainer, div);
                                    div.appendChild(span);
                                    div.appendChild(link);
                                    selectedItemsContainer.appendChild(div);
                                }
                            }

                            function removeConfigArrayItem(container, item) {
                                let config = editor.get();
                                var configItemsArray = getConfigValue(config, container.id);
                                const itemText = item.querySelector('span').textContent;
                                if (configItemsArray != undefined){
                                    const index = configItemsArray.indexOf(itemText);
                                    if (index !== -1) {
                                        configItemsArray.splice(index, 1);
                                    }
                                    editor.set(config);
                                }else{
                                    alert("This item was not defined in the configuration");
                                }
                                container.removeChild(item);
                            }
                            
               
                            function removeAllConfigArrayItems(container) {
                                if (confirm("Are you sure that you want to remove all items from this selection?")){
                                    let config = editor.get();
                                    var configItemsArray = getConfigValue(config, container.id);

                                    if (configItemsArray != undefined) {
                                        // Clear the configItemsArray
                                        configItemsArray.length = 0;

                                        // Remove all child elements from the container
                                        while (container.firstChild) {
                                            container.removeChild(container.firstChild);
                                        }

                                        // Update the config
                                        editor.set(config);
                                    } else {
                                        alert("This item was not defined in the configuration");
                                    }
                                }
                            }

                            function addConfigArrayItem(container, item) {
                                let configItemsArray = null
                                let config = editor.get();
                                configItemsArray = getConfigValue(config, container.id);
                                if (configItemsArray == undefined){
                                    setConfigValue(config, container.id, []);
                                    configItemsArray = getConfigValue(config, container.id);
                                }
                                
                                var itemText = null;
                                if (item.querySelector) {
                                    // item is an element, use querySelector to get the text content of the span
                                    itemText = item.querySelector('span') ? item.querySelector('span').textContent : '';
                                } else {
                                    // item is already text
                                    itemText = item;
                                }

                                if (configItemsArray == null){
                                    configItemsArray = [itemText];
                                }else{
                                    if (!configItemsArray.includes(itemText)) {
                                        configItemsArray.push(itemText);
                                    }
                                }

                                editor.set(config);
                            }

                            function addMarginGroup(margin_name=null, margin_config=null, is_new=false) {
                                if (is_new && margin_name == null){
                                    margin_name = prompt(`Enter your custom margin group name (ex: Firearms, Ammunition):`);
                                    if (margin_name == null){
                                        return;
                                    }
                                    var config = editor.get();
                                    config.pricing.margin[margin_name] = {
                                        "margin_dollar": config.pricing.margin.default.margin_dollar,
                                        "margin_percentage": config.pricing.margin.default.margin_percentage,
                                        "product_class": [],
                                        "sku": [],
                                        "category": [],
                                        "upc": [],
                                        "brand": []
                                    }
                                    editor.set(config);
                                    config = editor.get();
                                    margin_config = config.pricing.margin[margin_name];
                                }
                                const container = document.getElementById('margin-group-container');
                                const div = document.createElement('div');
                                div.className = 'group-container';
                                var groupId = `group-${margin_name ? margin_name : Date.now()}`;
        
                                div.innerHTML = `
                                    <div class="group-header">
                                        <div style="cursor:pointer;" class="accordion-header" onclick="toggleAccordion('${groupId}', this)">
                                            <i class="fas fa-plus accordion-toggle-icon"></i>${margin_name}
                                        </div>
                                        <i class="fas fa-trash-alt remove-link" title="Remove Item" onclick="removeMarginGroup(this, '${margin_name}')"></i>
                                    </div>
                                    <div class="accordion-content" id="${groupId}">
                                        <div class="helperDialog">Use the available filters below to specify which products to apply your custom margin</div>
                                        <div class="form-row">
                                            <label for="product_class">Product Class:</label>
                                            <div class="field-container">
                                                <div class="selected-items" id="pricing-margin-${margin_name}-product_class"></div>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="openModal('productClassModal', 'pricing-margin-${margin_name}-product_class')">Select</span>&nbsp;|&nbsp;
                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-margin-${margin_name}-product_class'))">Remove All</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <label>Category:</label>
                                            <div class="field-container">
                                                <div class="selected-items" id="pricing-margin-${margin_name}-category"></div>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="openModal('categoryModal', 'pricing-margin-${margin_name}-category')">Select</span>&nbsp;|&nbsp;
                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-margin-${margin_name}-category'))">Remove All</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <label for="brand">Brand:</label>
                                            <div class="field-container">
                                                <div class="selected-items" id="pricing-margin-${margin_name}-brand"></div>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="openModal('brandModal', 'pricing-margin-${margin_name}-brand')">Select</span>&nbsp;|&nbsp;
                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-margin-${margin_name}-brand'))">Remove All</span>
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <label for="sku">SKU:</label>
                                            <div class="field-container">
                                                <div class="selected-items" id="pricing-margin-${margin_name}-sku"></div>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="promptAndAddItems(this, 'sku', 'pricing-margin-${margin_name}-sku')">Select</span>&nbsp;|&nbsp;
                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-margin-${margin_name}-sku'))">Remove All</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <label for="upc">UPC:</label>
                                            <div class="field-container">
                                                <div class="selected-items" id="pricing-margin-${margin_name}-upc"></div>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="promptAndAddItems(this, 'upc', 'pricing-margin-${margin_name}-upc')">Select</span>&nbsp;|&nbsp;
                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-margin-${margin_name}-upc'))">Remove All</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <label for="margin_percentage">Margin (%):</label>
                                            <input class="field-number" type="number" name="margin_percentage" id="pricing-margin-${margin_name}-margin_percentage" min="0" max="0.99" step="0.01" value="${margin_config ? margin_config.margin_percentage : ''}"  data-autosave="true">
                                        </div>
                                        <div class="form-row">
                                            <label for="margin_dollar">Margin ($):</label>
                                            <input class="field-number" type="number" name="margin_dollar" id="pricing-margin-${margin_name}-margin_dollar" value="${margin_config ? margin_config.margin_dollar : ''}" data-autosave="true">
                                        </div>
                                        <div class="form-row">
                                            <label for="price_based_margin">Price Based Margin:</label>
                                            <div class="field-container">
                                                <table class="price-based-margin-table" id="price-based-margin-table-${groupId}">
                                                    <thead>
                                                        <tr style="white-space: nowrap;">
                                                            <th>Min ($)</th>
                                                            <th>Max ($)</th>
                                                            <th>Margin ($)</th>
                                                            <th>Margin (%)</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="add-item-container">
                                                    <span class="add-item" onclick="openModal('priceBasedMarginModal', 'price-based-margin-table-${groupId}')">Add Price Based Margin</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                container.appendChild(div);

                                if (margin_config!=null){
                                    filterElements = [
                                        {"name": "product_class", "selectedItemsContainer": "pricing-margin-" + margin_name + "-product_class"},
                                        {"name": "brand", "selectedItemsContainer": "pricing-margin-" + margin_name + "-brand"},
                                        {"name": "category", "selectedItemsContainer": "pricing-margin-" + margin_name + "-category"},
                                        {"name": "sku", "selectedItemsContainer": "pricing-margin-" + margin_name + "-sku"},
                                        {"name": "upc", "selectedItemsContainer": "pricing-margin-" + margin_name + "-upc"}
                                    ];
                                    for (const filterElement of filterElements) {
                                        if (margin_config[filterElement.name]){
                                            selectedItemsContainer = document.getElementById(`${filterElement.selectedItemsContainer}`);
                                            addSelectedItemsToContainer(selectedItemsContainer, margin_config[filterElement.name]);
                                        }
                                    }

                                    // load price-based margin table
                                    if (margin_config.price_based_margin){
                                        price_based_margin_table = document.getElementById(`price-based-margin-table-${groupId}`);
                                        for (const pbm of margin_config.price_based_margin){
                                            loadPriceBasedMargin(groupId, price_based_margin_table, pbm.minimum_unit_cost, pbm.maximum_unit_cost, pbm.margin_dollar, pbm.margin_percentage);
                                        }
                                    }

                                }
                                if (is_new){
                                    expandGroup(groupId, div);
                                    setupAutoSave();
                                }
                            }

                            function promptAndAddItems(element, itemType, containerId) {
                                const items = prompt(`Enter ${itemType}s separated by commas:`);
                                
                                if (items) {
                                    const itemArray = items.split(',');
                                    const container = document.getElementById(containerId);
                                    container.classList.remove('hidden');
                                    itemArray.forEach(item => {
                                        // add the item to the configuration
                                        addConfigArrayItem(container, item);

                                        // add theitem to theUI
                                        const div = document.createElement('div');
                                        div.className = 'selected-item';
                                        const span = document.createElement('span');
                                        span.textContent = item.trim();
                                        const link = document.createElement('i');
                                        link.className = 'fas fa-trash-alt remove-link';
                                        link.title = 'Remove item'; 
                                        link.onclick = () => removeConfigArrayItem(container, div);
                                        div.appendChild(span);
                                        div.appendChild(link);
                                        container.appendChild(div);
                                        
                                    });
                                }
                            }

                            function removeMarginGroup(link, group_name) {
                                
                                const config = editor.get();
                                delete config.pricing.margin[group_name];
                                editor.set(config);

                                const group = link.closest('.group-container');
                                group.parentNode.removeChild(group);
                            }

                            function toggleAccordion(groupId, element) {
                                const content = document.getElementById(groupId);
                                if (!content) {
                                    console.error(`Element with id ${groupId} not found.`);
                                    return;
                                }

                                const header = element.closest('.accordion-header');
                                if (!header) {
                                    console.error('Header element not found.');
                                    return;
                                }

                                const icon = header.querySelector('.accordion-toggle-icon');
                                if (!icon) {
                                    console.error(`Icon element with class 'accordion-toggle-icon' not found.`);
                                    return;
                                }

                                if (content.style.display === 'none' || content.style.display === '') {
                                    expandGroup(groupId, header.parentNode);
                                    icon.classList.add('fa-minus');
                                    icon.classList.remove('fa-plus');
                                } else {
                                    content.style.display = 'none';
                                    icon.classList.add('fa-plus');
                                    icon.classList.remove('fa-minus');
                                }
                            }


                            function expandGroup(groupId, groupElement) {
                                const content = document.getElementById(groupId);
                                const allGroups = document.querySelectorAll('.group-container .accordion-content');
                                allGroups.forEach(group => {
                                    if (group !== content) {
                                        group.style.display = 'none';
                                        group.previousElementSibling.querySelector('.accordion-toggle-icon').classList.add('fa-plus');
                                        group.previousElementSibling.querySelector('.accordion-toggle-icon').classList.remove('fa-minus');
                                    }
                                });
                                content.style.display = 'block';
                                groupElement.querySelector('.accordion-toggle-icon').classList.remove('fa-plus');
                                groupElement.querySelector('.accordion-toggle-icon').classList.add('fa-minus');
                            }

                            function updateGroupName(input) {
                                const groupName = input.closest('.accordion-content').previousElementSibling.querySelector('.group-name');
                                groupName.textContent = input.value;
                            }

                            function removeAllRowsExceptHeader(table) {
                                var rowCount = table.rows.length;
                                for (var i = rowCount - 1; i > 0; i--) {
                                    table.deleteRow(i);
                                }
                            }

                            function loadPriceBasedMargin(margin_group, tableBody, minPrice, maxPrice, marginDollar, marginPercentage){
                                const row = document.createElement('tr');
                                $(margin_group.id).empty();
                                const margin = {
                                    "minimum_unit_cost": minPrice,
                                    "maximum_unit_cost": maxPrice,
                                    "margin_dollar": marginDollar,
                                    "margin_percentage": marginPercentage
                                }
                                row.innerHTML = `
                                    <td>${minPrice}</td>
                                    <td>${maxPrice}</td>
                                    <td>${marginDollar}</td>
                                    <td>${marginPercentage}</td>
                                    <td><i class="fas fa-trash-alt remove-link" title="Remove Item" onclick="removePricedBasedMarginTier(this, '${margin_group}', ${minPrice}, ${maxPrice}, ${marginDollar}, ${marginPercentage})"></i></td>
                                `;
                                tableBody.appendChild(row);
                            }

                            function savePriceBasedMargin() {
                                const modal = document.getElementById('priceBasedMarginModal');
                                const selectedItemsId = modal.getAttribute('data-selected-items-id');
                                const tableBody = document.querySelector(`#${selectedItemsId} tbody`);
                                const minPrice = isNaN(parseFloat(document.getElementById('min_price').value)) ? 0 : parseFloat(document.getElementById('min_price').value);
                                const maxPrice = isNaN(parseFloat(document.getElementById('max_price').value)) ? 0 : parseFloat(document.getElementById('max_price').value);
                                const marginDollar = isNaN(parseFloat(document.getElementById('margin_dollar').value)) ? 0 : parseFloat(document.getElementById('margin_dollar').value);
                                const marginPercentage = isNaN(parseFloat(document.getElementById('margin_percentage').value)) ? 0 : parseFloat(document.getElementById('margin_percentage').value);
                                const config = editor.get();
                                var groupName = selectedItemsId.split("group-")[1];
                                const pbm = {
                                        "minimum_unit_cost": minPrice,
                                        "maximum_unit_cost": maxPrice,
                                        "margin_dollar": marginDollar,
                                        "margin_percentage": marginPercentage
                                }
                                
                                if (minPrice >= 0 && maxPrice>0 && maxPrice >= minPrice){
                                    if (!config.pricing.margin[groupName].price_based_margin){
                                        config.pricing.margin[groupName]['price_based_margin'] = [pbm];
                                    }else{
                                        config.pricing.margin[groupName].price_based_margin.push(pbm);
                                    }
                                    editor.set(config);
                                    loadPriceBasedMargin(groupName, tableBody, minPrice, maxPrice, marginDollar, marginPercentage);
                                }
                                closeModal('priceBasedMarginModal');
                            }

                            function removePricedBasedMarginTier(link, group, minCost, maxCost, marginDollar, marginPercentage) {
                                var config = editor.get();
                                if (group.startsWith("group-")){
                                    group = group.split("group-")[1];
                                }
                                var pbm = config.pricing.margin[group].price_based_margin;

                                // Filter out the elements that match the criteria
                                pbm = pbm.filter(item => 
                                    !(item.minimum_unit_cost === minCost &&
                                    item.maximum_unit_cost === maxCost &&
                                    item.margin_percentage === marginPercentage &&
                                    item.margin_dollar === marginDollar)
                                );

                                // Sort the remaining elements by minimum_distributor_cost in ascending order
                                pbm.sort((a, b) => a.minimum_unit_cost - b.minimum_unit_cost);

                                // Update the config with the filtered and sorted array
                                config.pricing.margin[group].price_based_margin = pbm;
                                editor.set(config);

                                // Remove the row from the DOM
                                const row = link.closest('tr');
                                row.parentNode.removeChild(row);
                            }


                            function sortTableByMinPrice(tableBody) {
                                const rowsArray = Array.from(tableBody.rows);
                                rowsArray.sort((a, b) => parseFloat(a.cells[0].innerText) - parseFloat(b.cells[0].innerText));
                                rowsArray.forEach(row => tableBody.appendChild(row));
                            }

                            // Add an initial margin group on page load
                            //window.onload = addMarginGroup;

                            // Close the modal when the user clicks anywhere outside of it
                            window.onclick = function(event) {
                                const modals = document.querySelectorAll('.modal');
                                modals.forEach(modal => {
                                    if (event.target === modal) {
                                        modal.style.display = "none";
                                    }
                                });
                            }
                        </script>
                        <style>
                            #unsaved-indicator {
                                text-align: left;
                                padding: 5px;
                                width: 100%;
                                display: none;
                            }
                            .validator_view {
                                text-align: left;
                                padding: 5px;
                                width: 100%;
                            }
                            .validator_view:empty {
                                height: 0;
                                padding: 0; /* Optionally, you can also remove padding if there's no content */
                            }
                            .card {
                                width: 220px;
                                margin-bottom: 10px;
                                padding: 5px;
                                position: relative;
                                height: 200px;
                            }

                            .distcards {
                                display: flex;
                                padding-left: 15px;
                                flex-wrap: wrap;
                                justify-content: center; /* Center elements horizontally */
                                align-content: center; /* Center elements vertically within the container */
                            }
                            
                            .targetcards {
                                display: flex;
                                padding-left: 15px;
                                flex-wrap: wrap;
                                gap: 10px;
                                justify-content: center; /* Center elements horizontally */
                                align-content: center; /* Center elements vertically within the container */
                            }

                            .card-header {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                padding: 5px;
                                background-color: #f8f9fa;
                                border-bottom: 1px solid #e9ecef;
                            }
                            .card-header .card-title {
                                margin-bottom: 5px;
                                font-size: 10px;
                            }
                            .toggle-switch {
                                position: relative;
                                display: inline-block;
                                width: 50px;
                                height: 25px;
                            }
                            .distid_image_area {
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                height: 100px;
                            }
                            .toggle-switch input {
                                opacity: 0;
                                width: 0;
                                height: 0;
                            }
                            .slider {
                                position: absolute;
                                cursor: pointer;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background-color: #ccc;
                                transition: .4s;
                                border-radius: 25px;
                            }
                            .slider:before {
                                position: absolute;
                                content: "";
                                height: 19px;
                                width: 19px;
                                left: 3px;
                                bottom: 3px;
                                background-color: white;
                                transition: .4s;
                                border-radius: 50%;
                            }
                            input:checked + .slider {
                                background-color: #2196F3;
                            }
                            input:checked + .slider:before {
                                transform: translateX(25px);
                            }
                            .email_template_header {
                                border-bottom: 2px solid #BBBBBB;
                                margin-top: 30px;
                                font-size: 14px;
                                margin-bottom: 20px;
                                font-weight: bold;
                                width:100%;
                                padding:5px;
                            }
                            .card-body {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                padding: 5px;
                            }
                            .action-links {
                                margin-top: 10px;
                                text-align: center;
                                cursor: pointer;
                                
                            }
                            .action-links .remove-link {
                                color: red;
                                margin-left: 5px;
                                text-decoration: underline;
                            }
                            .action-links span {
                                margin: 0 5px;
                                color: #2271b1;
                                text-decoration: underline;
                            }
                            .card-header img {
                                max-height: 75px;
                                max-width: 200px;
                                margin-bottom: 5px;
                            }
                            .form-group {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 10px; /* Adjust spacing between textareas as needed */
                            }
                            .form-groups-wrapper {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 10px; /* Adjust spacing between form groups as needed */
                            }

                            .form-group {
                                flex: 1; /* Allow form groups to grow and fill available space */
                                min-width: 300px; /* Optional: set a minimum width for the form groups */
                            }

                            input {
                                width: 100%; /* Ensure inputs take up full width of their container */
                            }

                            .textarea-wrapper {
                                display: flex;
                                flex-direction: column;
                                flex: 1; /* Allow textareas to grow and fill available space */
                            }

                            textarea {
                                width: 100%; /* Ensure textareas take up full width of their container */
                                min-width: 200px; /* Optional: set a minimum width for the textareas */
                            }

                            .nav-tabs {
                                border: 1px solid #ddd;
                                background-color: #eee;
                            }

                            .nav-tabs .nav-link {
                                border: 1px solid transparent;
                                color: black;
                            }

                            .nav-tabs .nav-link.active {
                                color: black;
                                border: 1px solid #ddd;
                                background-color: #dddddd;
                            }

                            .email-template-label {
                                font-style: italic;
                                color: #5F6D9D;
                                font-weight:bold;
                            }

                            #order_fulfillment_table a {
                                color: #2271b1;; /* Sets the link color within the specific div */
                            }

                            .pricing-assumptions-form-header {
                                font-size: 1.5em;
                                margin-bottom: 20px;
                                text-align: center;
                            }
                            .pricing-assumptions-form-row {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 10px;
                            }
                            .pricing-assumptions-form-row label {
                                text-align: right;
                                padding-right: 10px;
                            }
                           
                            .pricing-assumptions-form-row input[type="checkbox"] {
                                width: auto;
                                margin-left: 10px;
                            }
                            
                            .pricing-assumptions-form-header {
                                font-size: 1.5em;
                                margin-bottom: 20px;
                                text-align: center;
                            }
                            .pricing-assumptions-form-row {
                                display: flex;
                                justify-content: space-between;
                                align-items: top;
                                margin-bottom: 10px;
                            }
                            .pricing-assumptions-form-row label {
                                width: 45%;
                                text-align: right;
                                padding-right: 10px;
                            }
                            .pricing-assumptions-form-row input,
                            .pricing-assumptions-form-row select {
                                width: 45%;
                                text-align: left;
                            }
               
                            .pricing-tab-container {
                                display: flex;
                                padding: 10px;
                                align-items: flex-start; /* Align items to the top */
                            }

                            .pricing-assumptions-form-container {
                                width: 525px; /* or your preferred width */
                                margin-right: 20px; /* Adjust space between containers */
                                box-sizing: border-box;
                            }

                            .custom-margin-container {
                                width: 60%; /* or your preferred width */
                                box-sizing: border-box;
                            }
                            .form-container {
                                max-width: 800px;
                                margin: auto;
                                padding: 20px;
                                border: 1px solid #ccc;
                                border-radius: 5px;
                            }
                            .form-header {
                                font-size: 1.5em;
                                margin-bottom: 10px;
                            }
                            .form-row {
                                display: flex;
                                align-items: flex-start;
                                margin-bottom: 5px;
                            }
                            .form-row label {
                                flex: 0 0 150px;
                                margin-bottom: 5px;
                                align-self: flex-start;
                            }
                            .form-row .field-container {
                                flex: 1;
                                display: flex;
                                flex-wrap: wrap;
                                gap: 5px;
                            }

                            .field-number {
                                width:100px;
                                height:20px;
                            
                            }
                            .form-row input, .form-row select, .field-container {
                                padding: 8px;
                                box-sizing: border-box;
                            }
                            .array-container, .group-container {
                                margin-bottom: 15px;
                                border: 1px solid #ccc;
                                padding: 10px;
                                border-radius: 5px;
                            }
                            .array-container.hidden {
                                border: none;
                                padding: 0;
                            }
                            .selected-item {
                                display: flex;
                                align-items: center;
                                margin-bottom: 5px;
                                background-color: #e9e9e9;
                                padding: 5px 10px;
                                border-radius: 5px;
                            }
                            .selected-item span {
                                margin-right: 10px;
                            }
                            .add-item, .add-group, .accordion-toggle {
                                cursor: pointer;
                                color: #2271b1;
                                text-decoration: underline;
                                display: inline-block;
                            }
                            .remove-link {
                                color: #f1afae;
                                cursor: pointer;
                                margin-left: 10px;
                            }
                            .accordion-content {
                                display: none;
                                margin-top: 10px;
                            }
                            .group-header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            }
                            .group-header .form-row {
                                flex: 1;
                                margin-bottom: 0;
                            }
                            .accordion-toggle-icon {
                                margin-right: 10px;
                                cursor: pointer;
                                border: 1px solid #000;
                                padding: 2px;
                                border-radius: 3px;
                                display: inline-block;
                                width: 20px;
                                text-align: center;
                            }
                            .group-name {
                                font-weight: bold;
                                cursor: pointer;
                                flex: 1;
                            }
                            .modal {
                                display: none;
                                position: fixed;
                                left: 0;
                                top: 0;
                                width: 100%;
                                height: 100%;
                                overflow: auto;
                                background-color: rgba(0,0,0,0.4);
                                padding-top: 60px;
                            }
                            .cockpit-modal-content {
                                background-color: #fefefe;
                                margin: 5% auto;
                                padding: 20px;
                                border: 1px solid #888;
                                width: 500px;
                                box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
                                position: relative;
                                
                            }
                            .close {
                                color: red;
                                float: right;
                                font-size: 28px;
                                font-weight: bold;
                                cursor: pointer;
                            }
                            .close:hover,
                            .close:focus {
                                color: darkred;
                                text-decoration: none;
                            }
                            .selected-items {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 5px;
                                overflow-y: auto;
                                max-height:750px;
                            }
                            .price-based-margin-table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 10px;
                            }
                            .price-based-margin-table th, .price-based-margin-table td {
                                border: 1px solid #ddd;
                                padding: 8px;
                            }
                            .price-based-margin-table th {
                                background-color: #f2f2f2;
                                text-align: left;
                            }
                            
                            .modalPopupContent {
                                flex-grow: 1;
                                height:300px;
                                overflow-y: auto;
                            }
                            
                            .add-item-container {
                                display: flex;
                                justify-content: flex-start;
                            }
                            
                            .helperDialog {
                                margin:10px;
                                padding:10px;
                                text-align:left;
                                width:95%;
                                border:solid gray 1px;
                                background:#e1edf1;
                                
                                font-weight:normal;
                            }
                            .helperDialog {
                                background-color: #e6f4f9;
                                border: 1px solid #ccc;
                                padding: 10px;
                                font-family: Arial, sans-serif;
                                font-size: 14px;
                            }
                            .helperDialog {
                                background-color: #e6f4f9;
                                border: 1px solid #ccc;
                                padding: 10px;
                                font-family: Arial, sans-serif;
                                font-size: 14px;
                            }
                            .helperDialog ul {
                                list-style-type: disc;
                                padding-left: 30px; /* Increase padding to indent the list items */
                                margin-top: 5px;
                            }
                            .helperDialog ul li {
                                margin-bottom: 5px;
                            }
                            .helperDialog .important-tips {
                                font-weight: bold;
                                margin-left: 20px; /* Indent the "Important Tips:" text */
                            }
                            .optionFilterInput {
                                margin-bottom:15px;
                            }
                            .main-container {
                                display: flex;
                                justify-content: space-between;
                            }

                            .left-container, .right-container {
                                width: 48%;
                                padding: 10px;
                                border: 1px solid #ccc;
                                border-radius: 5px;
                                margin-top:10px;
                            }

                            .other-restrictions-header {
                                text-align: center;
                                margin-bottom: 10px;
                                font-size: 1.2em;
                                font-weight: bold;
                            }

                            .restriction-section {
                                border: 1px solid #ccc;
                                padding: 10px;
                                margin-bottom: 10px;
                                border-radius: 5px;
                            }

                            .restriction-header {
                                text-align: center;
                                margin-bottom: 10px;
                                font-size: 1.2em;
                                font-weight: bold;
                            }

                            .restriction-container {
                                display: flex;
                                justify-content: space-between;
                            }

                            .restriction-item {
                                display: flex;
                                flex-direction: column;
                                gap: 5px;
                                width: 48%;
                            }

                            .field-container {
                                display: flex;
                                flex-direction: column;
                                gap: 10px;
                            }

                            .add-item-container {
                                text-align: center;
                            }

                            #delete_gunbroker_listings_button {
                                margin-top:50px;
                                background-color: red;
                                display: block;
                                margin: 0 auto;
                                color: white; /* Ensures text is readable */
                            }

                            .selected-items {
                                flex-grow: 1;
                            }
                            .modalHeader{
                                font-weight:bold;
                                font-size:15pt;
                                padding:5px;
                            }
                            .cost-restrictions {
                                display: flex;
                                justify-content: space-between;
                            }

                            .cost-field {
                                flex: 1;
                                margin-right: 10px; /* Adds some space between the two fields */
                            }

                            .cost-field:last-child {
                                margin-right: 0; /* Removes margin from the last child */
                            }

                            .cost-field label {
                                display: block;
                                margin-bottom: 5px;
                            }

                            .cost-field input {
                                width: 100%;
                                padding: 5px;
                                box-sizing: border-box;
                            }
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                           
                            .operand_selected {
                                font-weight: bold;
                                text-decoration: underline;
                            }
  

                            .cockpit-watermark {
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                color: red;
                                font-size: 10px;
                                font-weight: bold;
                                background: rgba(255, 255, 255, 0.7); /* Optional: Add background to improve readability */
                                padding: 2px 2px; /* Optional: Add some padding */
                                border-radius: 3px; /* Optional: Add rounded corners */
                            }
                            .cockpit-product-search-container {
                                display: flex;
                                align-items: center;
                                width:50%;
                                margin-bottom:20px;
                            }

                        </style>

                        <!-- Modal -->
                        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true" >
                            <div id="modal_dialog" class="modal-dialog modal-dialog-centered">
                                <div class="cockpit-modal-content modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailsModalLabel">Distributor Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="modal-body">
                                        <!-- Form fields will be populated here -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="form-table">
                            <tr valign="top">
                                <td colspan=3>
                                        <div class="ui_form_container">                                        
                                         <div id="brandModal" class="modal">
                                            <div class="cockpit-modal-content modal-content">
                                                <span class="close" onclick="closeModal('brandModal')">&times;</span>
                                                <span class="modalHeader">Select Brands</span>
                                                <div id="brandModalSearchDiv"></div>
                                                <div class="modalPopupContent" id="brandModalList"></div>
                                                <button style="margin-top:50px;" class="btn btn-primary" onclick="saveSelections('brandModal', 'brand')">Apply</button>
                                            </div>
                                        </div>
                                        <div id="ignoreMapBrandModal" class="modal">
                                            <div class="cockpit-modal-content modal-content">
                                                <span class="close" onclick="closeModal('ignoreMapBrandModal')">&times;</span>
                                                <span class="modalHeader">Select Brands</span>
                                                <div id="ignoreMapBrandModalSearchDiv"></div>
                                                <div class="modalPopupContent" id="ignoreMapBrandModalList"></div>
                                                <button style="margin-top:50px;" class="btn btn-primary" onclick="saveSelections('ignoreMapBrandModal', 'brand')">Apply</button>
                                            </div>
                                        </div>
                                        <div id="categoryModal" class="modal">
                                            <div class="cockpit-modal-content modal-content">
                                                <span class="close" onclick="closeModal('categoryModal')">&times;</span>
                                                <span class="modalHeader">Select Categories</span>
                                                <div id="categoryModalSearchDiv"></div>
                                                <div class="modalPopupContent" id="categoryModalList"></div>
                                                <button style="margin-top:50px;" class="btn btn-primary" onclick="saveSelections('categoryModal', 'category')">Apply</button>
                                            </div>
                                        </div>

                                        <div id="productClassModal" class="modal">
                                            <div class="cockpit-modal-content modal-content">
                                                <span class="close" onclick="closeModal('productClassModal')">&times;</span>
                                                <span  class="modalHeader">Select Product Classes</span>
                                                <div class="modalPopupContent" id="productClassModalList"></div>
                                                <button style="margin-top:50px;" class="btn btn-primary" onclick="saveSelections('productClassModal', 'product_class')">Apply</button>
                                            </div>
                                        </div>

                                        <ul class="nav nav-tabs" id="configTabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="distributors-tab" data-toggle="tab" href="#distributors" role="tab" aria-controls="distributors" aria-selected="false">Distributors</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="product-restrictions-tab" data-toggle="tab" href="#product-restrictions" role="tab" aria-controls="product-restrictions" aria-selected="false">Product Restrictions</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="targets-tab" data-toggle="tab" href="#targets" role="tab" aria-controls="targets" aria-selected="false">Targets</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pricing-tab" data-toggle="tab" href="#pricing" role="tab" aria-controls="pricing" aria-selected="true">Pricing</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="notifications-config-tab" data-toggle="tab" href="#notifications-config" role="tab" aria-controls="notifications-config" aria-selected="false">Notifications</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="store-config-tab" data-toggle="tab" href="#store-config" role="tab" aria-controls="store-config" aria-selected="false">Store/Location</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="classic-configurator-tab" data-toggle="tab" href="#classic-configurator" role="tab" aria-controls="classic-configurator" aria-selected="false">Advanced</a>
                                            </li>
                                        </ul>
                  
                                        <div  class="tab-content" id="configTabContent">
                                            <div class="overlay" id="configuration_loading_overlay" style="display:flex;position: absolute;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.7);justify-content: center;align-items: center;">
                                                <div class="loader" style="border: 8px solid #f3f3f3;border-top: 8px solid #3498db;border-radius: 50%;width: 50px;height: 50px;animation: spin 1s linear infinite;"></div>
                                            </div>
                                            <div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
                                            <div class="helperDialog"><strong>Pricing/Margin settings</strong> allow you to fine-tune list prices of your product on your site. There are core pricing assumptions, and custom margin groups you can setup to price specific types of products accordingly. <strong>Important:</strong> Margin settings in percentage should be entered in their decimal format. (Examples: 25% = 0.25, 2.49% = 0.0249...etc)</div>
                                            <div class="pricing-tab-container">
                                                    <!-- Pricing Form Content -->
                                                    <div class="pricing-assumptions-form-container core-pricing-container">
                                                        <div class="pricing-assumptions-form-header">Core Pricing Assumptions</div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-margin-default-margin_percentage">Default Margin (%):</label>
                                                            <input type="number" id="pricing-margin-default-margin_percentage" name="pricing-margin-default-margin_percentage" min="0" max="0.99" step="0.01"  data-autosave="true">
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-margin-default-margin_dollar">Default Margin ($):</label>
                                                            <input type="number" id="pricing-margin-default-margin_dollar" name="pricing-margin-default-margin_dollar"  data-autosave="true">
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-rounding">Price Rounding:</label>
                                                            <select id="pricing-rounding" name="pricing-rounding"  data-autosave="true">
                                                                <option value="none">None</option>
                                                                <option value="round_to_nearest_dollar">Round to Nearest Dollar</option>
                                                                <option value="round_up_to_99_cents">Round up to 99 Cents</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="helperDialog">Sales Tax Assumption and Credit Card fees help Cockpit calculate the total cost of a customer's order. If you include Credit Card fees, we can accurately determine these charges based on the overall order value.</div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-include_credit_card_fees_in_price">Include CC Fees in Price:</label>
                                                            <label style="align:left;width:50px;" class="toggle-switch">
                                                                <input type="checkbox" id="pricing-include_credit_card_fees_in_price" name="pricing-include_credit_card_fees_in_price"  data-autosave="true">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-credit_card_fee_percent">CC Fee Percent:</label>
                                                            <input type="number" id="pricing-credit_card_fee_percent" name="pricing-credit_card_fee_percent" min="0" max="0.99" step="0.01"  data-autosave="true">
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-credit_card_fee_transaction">CC Fee (per Transaction):</label>
                                                            <input type="number" id="pricing-credit_card_fee_transaction" name="pricing-credit_card_fee_transaction"  data-autosave="true">
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="pricing-sales_tax_assumption">Sales Tax Assumption:</label>
                                                            <input type="number" id="pricing-sales_tax_assumption" name="pricing-sales_tax_assumption" min="0" max="0.99" step="0.01"  data-autosave="true">
                                                        </div>
                                                        <div class="helperDialog"><strong>Sell at MAP</strong> prices all products at the MAP price, overriding margin settings if there is a MAP on the product. When deactivated, products are priced at MAP only if the margin-based list price falls below MAP.</div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label for="sell_at_map">Sell at MAP:</label>
                                                            <label style="align:left;width:50px;" class="toggle-switch">
                                                                <input type="checkbox" id="pricing-sell_at_map" name="pricing-sell_at_map" data-autosave="true">
                                                                <span class="slider"></span>
                                                            </label>
                                                        </div>
                                                        <div class="pricing-assumptions-form-row">
                                                            <label nowrap for="ignore_map_brands">Ignore&nbsp;MAP&nbsp;Brands:</label>
                                                            <div class="field-container">
                                                                <div class="selected-items" id="pricing-ignore_map_brands"></div>
                                                                <div class="add-item-container">
                                                                    <span class="add-item" onclick="openModal('ignoreMapBrandModal', 'pricing-ignore_map_brands')">Select</span>&nbsp;|&nbsp;
                                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('pricing-ignore_map_brands'))">Remove All</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <label for="price-based-margin-table-default">Price Based Margin:</label>
                                                            <div class="field-container">
                                                                <table class="price-based-margin-table" id="price-based-margin-table-group-default">
                                                                    <thead>
                                                                        <tr style="white-space: nowrap;">
                                                                            <th>Min ($)</th>
                                                                            <th>Max ($)</th>
                                                                            <th>Margin ($)</th>
                                                                            <th>Margin (%)</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                                <div class="add-item-container">
                                                                    <span class="add-item" onclick="openModal('priceBasedMarginModal', 'price-based-margin-table-group-default')">Add Price Based Margin</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="pricing-assumptions-form-container custom-margin-container">
                                                        <div class="pricing-assumptions-form-header">Custom Margin Settings</div>
                                                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                                                        <div class="form-container">
                                                            <div id="margin-group-container"></div>
                                                            <span class="add-group" onclick="addMarginGroup(null,null,true)">Add Margin Group</span>
                                                        </div>
                                                        <div id="priceBasedMarginModal" class="modal">
                                                            <div class="cockpit-modal-content modal-content">
                                                                <span class="close" onclick="closeModal('priceBasedMarginModal')">&times;</span>
                                                                <span class="modalHeader">Add Price Based Margin</span>
                                                                <div>
                                                                    <label for="min_price">Min Price ($):</label>
                                                                    <input type="number" id="min_price" value="0">
                                                                </div>
                                                                <div>
                                                                    <label for="max_price">Max Price ($):</label>
                                                                    <input type="number" id="max_price" value="0">
                                                                </div>
                                                                <div>
                                                                    <label for="margin_percentage">Margin (%):</label>
                                                                    <input type="number" id="margin_percentage"  value="0">
                                                                </div>
                                                                <div>
                                                                    <label for="margin_dollar">Margin ($):</label>
                                                                    <input type="number" id="margin_dollar" value="0">
                                                                </div>
                                                                <button style="margin-top:50px;" class="btn btn-primary" onclick="savePriceBasedMargin()">Add</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="targets" role="tabpanel" aria-labelledby="targets-tab">
                                                <!-- Targets Form Content -->
                                                <div class="helperDialog"><strong>Targets</strong> are representing where you want your product to be listed. We support listing product on your site (WooCommerce), we support feeding aggregators like WikiArms, AmmoSeek, and Gun.Deals. These aggregators require you subscribe with them and pay a monthly fee to list products. We also support feeding product (distributor-based only, no local inventory) to Gunbroker. Add the Targets you wish to use, and only add those you are subscribed to. The toggle in each card below represents whether or not the feed is activated.</div>
                                                <div>
                                                    <div class="row" id="targets-container">
                                                        <!-- Existing targets will be loaded here -->
                                                    </div>
                                                    <div class="d-flex align-items-center mt-2" id="targets-select-container">
                                                        <select class="form-control mr-2" id="available-targets">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </select>
                                                        <button class="button alt" id="add-target">Add Target</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="notifications-config" role="tabpanel" aria-labelledby="notifications-tab">
                                            <div class="helperDialog"><strong>A note about notifications: </strong>Set up your notification settings using the forms below to customize your email templates. The notification email is where you will receive FFL Cockpit system alerts, including distributor order information. Use the templates below to modify the messages sent to your customers at various stages of the order fulfillment process. </div>
                                                <!-- Fulfillment Form Content -->
                                                <script>
                                                    const email_templates = [
                                                        {"type": "subject", "group_label":"Email Template: <strong>Order Processed</strong>", "label":"Order Processed (FFL Order) Subject", "id": "order_processed_subject", "config_key":"fulfillment-emailer-templates-order_processed-subject"},
                                                        {"type": "body", "label":"FFL Order", "id": "order_processed_ffl", "config_key":"fulfillment-emailer-templates-order_processed-message_ffl_order"},
                                                        {"type": "body", "label":"Non-FFL Order", "id": "order_processed_nonffl", "config_key":"fulfillment-emailer-templates-order_processed-message_non_ffl_order"},
                                                        {"type": "subject","group_label":"Email Template: <strong>Order Shipped</strong>", "label":"Order Shipped (FFL Order) Subject", "id": "order_shipped_subject", "config_key":"fulfillment-emailer-templates-order_shipped-subject"},
                                                        {"type": "body", "label":"FFL Order", "id": "order_shipped_ffl", "config_key":"fulfillment-emailer-templates-order_shipped-message_ffl_order"},
                                                        {"type": "body", "label":"Non-FFL Order", "id": "order_shipped_nonffl", "config_key":"fulfillment-emailer-templates-order_shipped-message_non_ffl_order"},
                                                        {"type": "subject","group_label":"Email Template: <strong>Order Delivered</strong>", "label":"Order Delivered (FFL Order) Subject", "id": "order_delivered_subject", "config_key":"fulfillment-emailer-templates-order_delivered-subject"},
                                                        {"type": "body", "label":"FFL Order", "id": "order_delivered_ffl", "config_key":"fulfillment-emailer-templates-order_delivered-message_ffl_order"},
                                                        {"type": "body", "label":"Non-FFL Order", "id": "order_delivered_nonffl", "config_key":"fulfillment-emailer-templates-order_delivered-message_non_ffl_order"},
                                                        {"type": "subject", "group_label": "Email Template: <strong>FFL Document Request</strong>", "label":"FFL Document Request Subject", "id": "ffl_request_subject", "config_key":"fulfillment-emailer-templates-ffl_request-subject"},
                                                        {"type": "body", "label":"FFL Document Request", "id": "ffl_request", "config_key":"fulfillment-emailer-templates-ffl_request-message"}
                                                    ];

                                                    const container = document.getElementById('notifications-config');
                                                    container.innerHTML += `
                                                        <div class="form-groups-wrapper">
                                                            <div class="form-group">
                                                                <label class="email_template_header" for="notification_email">Email to receive FFL Cockpit System Notifications</label>
                                                                <input placeholder="Notification Email" type="text" class="form-control" style="width:300px;" id="notification_email" data-autosave="true">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="email_template_header" for="from_email">Email for sending order updates to your customers</label>
                                                                <input placeholder="From Email" type="text" class="form-control" style="width:300px;" id="fulfillment-emailer-from_email" data-autosave="true">
                                                            </div>
                                                        </div>
                                                    `;

                                                    let currentDiv = null;
                                                    for (const email_template of email_templates) {
                                                        if (email_template.type === "subject") {
                                                            if (currentDiv) {
                                                                container.appendChild(currentDiv);
                                                            }
                                                            currentDiv = document.createElement('div');
                                                            currentDiv.className = 'form-group';
                                                            currentDiv.innerHTML = `
                                                                <label class="helperDialog" for="${email_template.config_key}">${email_template.group_label}</label>
                                                                <label class="email-template-label">Subject</i></label>
                                                                <input style="margin-bottom:20px;" placeholder="Email Subject" type="text" class="form-control" id="${email_template.config_key}" data-autosave="true">
                                                            `;
                                                        } else {
                                                            const wrapperDiv = document.createElement('div');
                                                            wrapperDiv.className = 'textarea-wrapper';

                                                            const label = document.createElement('label');
                                                            label.className = 'email-template-label';
                                                            label.setAttribute('for', email_template.config_key);
                                                            label.textContent = email_template.label;
                                                            wrapperDiv.appendChild(label);

                                                            const textarea = document.createElement('textarea');
                                                            textarea.id = email_template.config_key;
                                                            textarea.name = email_template.config_key;
                                                            textarea.setAttribute('data-autosave', 'true');
                                                            wrapperDiv.appendChild(textarea);

                                                            currentDiv.appendChild(wrapperDiv);
                                                        }
                                                    }
                                                    if (currentDiv) {
                                                        container.appendChild(currentDiv);
                                                    }

                                                    // Initialize TinyMCE for each textarea
                                                    email_templates.forEach(template => {
                                                        if (template.type === 'body') {
                                                            tinymce.init({
                                                                selector: `#${template.config_key}`,
                                                                plugins: 'code link lists image',
                                                                toolbar: 'code link image | bold italic backcolor forecolor | numlist bullist',
                                                                license_key: 'gpl',
                                                                menubar: false,
                                                                branding: false,
                                                                statusbar: false,
                                                                setup: function (mceeditor) {
                                                                    mceeditor.on('input', function () {
                                                                        clearTimeout(typingTimer);
                                                                        typingTimer = setTimeout(() => autoSave(mceeditor.getElement()), doneTypingInterval);
                                                                    });

                                                                    mceeditor.on('keydown', function () {
                                                                        clearTimeout(typingTimer);
                                                                    });

                                                                    mceeditor.on('blur', function () {
                                                                        autoSave(mceeditor.getElement());
                                                                    });
                                                                }
                                                            });
                                                        }
                                                    });
                                                </script>

                                            </div>
                                            <div class="tab-pane fade" id="store-config" role="tabpanel" aria-labelledby="store-tab">
                                                <!-- Store Config Form Content -->
                                                <!-- Pricing Form Content -->
                                                <div class="helperDialog"><strong>Store/Location Information</strong>Your FFL number and store information is needed for certain distributors to place a "ship-to-store" order. Ship-to-Store orders are usually required when there may be a drop-ship restriction on a brand, and you have to have the item shipped locatlly before reshipping the product out to your customer. </div>
                                                <div style="margin:10px;" class="pricing-assumptions-form-container">
                                                    <div class="pricing-assumptions-form-header">Store/Location Information</div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-ffl">FFL License Number:</label>
                                                        <input type="text" id="fulfillment-ship_to_store-ffl" name="fulfillment-ship_to_store-ffl" data-autosave="true">
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-phone">Phone Number:</label>
                                                        <input type="number" id="fulfillment-ship_to_store-phone" name="fulfillment-ship_to_store-phone" data-autosave="true">
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-address-ship_to_name">Business/Name:</label>
                                                        <input type="text" id="fulfillment-ship_to_store-address-ship_to_name" name="fulfillment-ship_to_store-address-ship_to_name"  data-autosave="true">
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-address-address_street">Street Address:</label>
                                                        <input type="text" id="fulfillment-ship_to_store-address-address_street" name="fulfillment-ship_to_store-address-address_street"  data-autosave="true">
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-address-city">City:</label>
                                                        <input type="text" id="fulfillment-ship_to_store-address-city" name="fulfillment-ship_to_store-address-city"  data-autosave="true">
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-address-state">State:</label>
                                                        <select id="fulfillment-ship_to_store-address-state" name="fulfillment-ship_to_store-address-state"  data-autosave="true"></select>
                                                        <script>
                                                            const dropdown = document.getElementById('fulfillment-ship_to_store-address-state');
                                                            statesAndTerritories.forEach(state => {
                                                                const option = document.createElement('option');
                                                                option.value = state.value;
                                                                option.textContent = state.text;
                                                                dropdown.appendChild(option);
                                                            });
                                                        </script>
                                                    </div>
                                                    <div class="pricing-assumptions-form-row">
                                                        <label for="fulfillment-ship_to_store-address-postal_code">Zip Code:</label>
                                                        <input type="text" id="fulfillment-ship_to_store-address-postal_code" name="fulfillment-ship_to_store-address-postal_code"  data-autosave="true">
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show active" id="distributors" role="tabpanel" aria-labelledby="distributors-tab">
                                                <!-- Distributors Form Content -->
                                                    <div class="helperDialog"><strong>Distributors (or Sources)</strong> allow you select where you want to pull the catalog data from to list products on your site. Select all of your distributors you'd like, making sure that you click on the Configure links to enter in your account credentials and other important information. The toggle in each card is showing whether or not the distributor is activated and feeding product to your site.</div>
                                                    <div>
                                                        <div class="row" id="distributors-container">
                                                            <!-- Existing distributors will be loaded here -->
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2" id="distributor-select-container">
                                                            <select class="form-control mr-2" id="available-distributors">
                                                                <!-- Options will be populated by JavaScript -->
                                                            </select>
                                                            <button class="button alt" id="add-distributor">Add Distributor</button>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        let initialCockpitConfiguration = null;
                                                        let distributorsSchema = null;
                                                        let targetSchema = null;
                                                        
                                                        async function get_distributors_schema() {
                                                            if (distributorsSchema == null) {
                                                                try {
                                                                    const response = await fetch("https://ffl-api.garidium.com", {
                                                                        method: "POST",
                                                                        headers: {
                                                                            "Accept": "application/json",
                                                                            "Content-Type": "application/json",
                                                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>"
                                                                        },
                                                                        body: JSON.stringify({ "action": "get_distributors_schema" })
                                                                    });

                                                                    const data = await response.json();
                                                                    distributorsSchema = data;
                                                                } catch (error) {
                                                                    console.error('Error fetching target schema:', error);
                                                                    return null; // Return null or handle the error as needed
                                                                }
                                                            }
                                                            
                                                            return distributorsSchema;
                                                        }

                                                        async function get_target_schema() {
                                                            if (targetSchema == null) {
                                                                try {
                                                                    const response = await fetch("https://ffl-api.garidium.com", {
                                                                        method: "POST",
                                                                        headers: {
                                                                            "Accept": "application/json",
                                                                            "Content-Type": "application/json",
                                                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>"
                                                                        },
                                                                        body: JSON.stringify({ "action": "get_targets_schema" })
                                                                    });

                                                                    const data = await response.json();
                                                                    targetSchema = data;
                                                                } catch (error) {
                                                                    console.error('Error fetching target schema:', error);
                                                                    return null; // Return null or handle the error as needed
                                                                }
                                                            }
                                                            
                                                            return targetSchema;
                                                        }

                                                        function load_fancy_editor(config){
                                                            $('#distributors-container').empty();
                                                            $('#targets-container').empty();
                      
                                                            console.log(">>Adding Distributor Forms");
                                                            for (let key in config.distributors) {
                                                                addDistributorForm(key, config.distributors[key]);
                                                            }

                                                            console.log(">>Adding Target Forms");
                                                            for (let key in config.targets) {
                                                                addTargetForm(key, config.targets[key]);
                                                            }
                                                            
                                                            //pricing fields
                                                            document.getElementById("pricing-margin-default-margin_percentage").value = config.pricing.margin.default.margin_percentage;
                                                            document.getElementById("pricing-margin-default-margin_dollar").value = config.pricing.margin.default.margin_dollar;
                                                            document.getElementById("pricing-sales_tax_assumption").value = config.pricing.sales_tax_assumption;
                                                            document.getElementById("pricing-credit_card_fee_percent").value = config.pricing.credit_card_fee_percent;
                                                            document.getElementById("pricing-credit_card_fee_transaction").value = config.pricing.credit_card_fee_transaction;
                                                            
                                                            document.getElementById("pricing-rounding").value = config.pricing.rounding;
                                                            //document.getElementById("pricing-include_shipping_in_price").checked = config.pricing.include_shipping_in_price;
                                                            document.getElementById("pricing-include_credit_card_fees_in_price").checked = config.pricing.include_credit_card_fees_in_price;
                                                            document.getElementById("pricing-sell_at_map").checked = config.pricing.sell_at_map;
                                                            
                                                            $('#pricing-ignore_map_brands').empty();
                                                            if (config.pricing.ignore_map_brands){
                                                                addSelectedItemsToContainer(document.getElementById("pricing-ignore_map_brands"), config.pricing.ignore_map_brands);
                                                            }
                                                            
                                                            // load price-based default margin table
                                                            if (config.pricing.margin.default.price_based_margin){
                                                                price_based_margin_table = document.getElementById(`price-based-margin-table-group-default`);
                                                                removeAllRowsExceptHeader(price_based_margin_table);
                                                                for (const pbm of config.pricing.margin.default.price_based_margin){
                                                                    loadPriceBasedMargin("group-default", price_based_margin_table, pbm.minimum_unit_cost, pbm.maximum_unit_cost, pbm.margin_dollar, pbm.margin_percentage);
                                                                }
                                                            }
                                                            
                                                            $('#margin-group-container').empty();
                                                            for (let key in config.pricing.margin) {
                                                                if (key!="default"){
                                                                    addMarginGroup(key, config.pricing.margin[key]);
                                                                }
                                                            }
                                                            
                                                            // Ship-to-Store/Location Info
                                                            document.getElementById("fulfillment-ship_to_store-address-ship_to_name").value = config.fulfillment.ship_to_store.address.ship_to_name;
                                                            document.getElementById("fulfillment-ship_to_store-ffl").value = config.fulfillment.ship_to_store.ffl;
                                                            document.getElementById("fulfillment-ship_to_store-phone").value = config.fulfillment.ship_to_store.phone?config.fulfillment.ship_to_store.phone:"";
                                                            document.getElementById("fulfillment-ship_to_store-address-address_street").value = config.fulfillment.ship_to_store.address.address_street;
                                                            document.getElementById("fulfillment-ship_to_store-address-city").value = config.fulfillment.ship_to_store.address.city;
                                                            document.getElementById("fulfillment-ship_to_store-address-postal_code").value = config.fulfillment.ship_to_store.address.postal_code;
                                                            document.getElementById("fulfillment-ship_to_store-address-state").value = config.fulfillment.ship_to_store.address.state;
                                                            
                                                            
                                                            // load email templates
                                                            document.getElementById("notification_email").value = config.notification_email;
                                                            document.getElementById("fulfillment-emailer-from_email").value = config.fulfillment.emailer.from_email;
                                                            
                                                            document.getElementById("fulfillment-emailer-templates-ffl_request-subject").value = config.fulfillment.emailer.templates.ffl_request.subject;
                                                            tinymce.get("fulfillment-emailer-templates-ffl_request-message").setContent(config.fulfillment.emailer.templates.ffl_request.message);
                                                            
                                                            document.getElementById("fulfillment-emailer-templates-order_processed-subject").value = config.fulfillment.emailer.templates.order_processed.subject;
                                                            tinymce.get("fulfillment-emailer-templates-order_processed-message_ffl_order").setContent(config.fulfillment.emailer.templates.order_processed.message_ffl_order);
                                                            tinymce.get("fulfillment-emailer-templates-order_processed-message_non_ffl_order").setContent(config.fulfillment.emailer.templates.order_processed.message_non_ffl_order);

                                                            document.getElementById("fulfillment-emailer-templates-order_shipped-subject").value = config.fulfillment.emailer.templates.order_shipped.subject;
                                                            tinymce.get("fulfillment-emailer-templates-order_shipped-message_ffl_order").setContent(config.fulfillment.emailer.templates.order_shipped.message_ffl_order);
                                                            tinymce.get("fulfillment-emailer-templates-order_shipped-message_non_ffl_order").setContent(config.fulfillment.emailer.templates.order_shipped.message_non_ffl_order);

                                                            document.getElementById("fulfillment-emailer-templates-order_delivered-subject").value = config.fulfillment.emailer.templates.order_delivered.subject;
                                                            tinymce.get("fulfillment-emailer-templates-order_delivered-message_ffl_order").setContent(config.fulfillment.emailer.templates.order_delivered.message_ffl_order);
                                                            tinymce.get("fulfillment-emailer-templates-order_delivered-message_non_ffl_order").setContent(config.fulfillment.emailer.templates.order_delivered.message_non_ffl_order);

                                                            // Product Restrictions
                                                            document.getElementById("product_restrictions-min_quantity_to_list").value = config.product_restrictions.min_quantity_to_list;
                                                            document.getElementById("product_restrictions-cost-global_restrictions-min_distributor_cost").value = config.product_restrictions.cost.global_restrictions.min_distributor_cost;
                                                            document.getElementById("product_restrictions-cost-global_restrictions-max_distributor_cost").value = config.product_restrictions.cost.global_restrictions.max_distributor_cost;
                                                            
                                                            const restrictions = ["product_class","category","brand","sku","upc"];
                                                            const include_excludes = ["include","exclude"];
                                                        
                                                            restrictions.forEach(restriction => {
                                                                if (config.product_restrictions[restriction] && config.product_restrictions[restriction]['include_operand']){
                                                                    toggleOperandOption(`product_restrictions-${restriction}`, config.product_restrictions[restriction].include_operand);
                                                                }else{
                                                                    toggleOperandOption(`product_restrictions-${restriction}`, "AND");
                                                                }
                                                            });
                                                            

                                                            restrictions.forEach(restriction => {
                                                                include_excludes.forEach(include_exclude => {
                                                                    if (config.product_restrictions[restriction][include_exclude]){
                                                                        $(`#product_restrictions-${restriction}-${include_exclude}`).empty();
                                                                        addSelectedItemsToContainer(document.getElementById(`product_restrictions-${restriction}-${include_exclude}`), config.product_restrictions[restriction][include_exclude]);
                                                                    }
                                                                });
                                                            });
                                                            populateAvailableDistributors();
                                                            populateAvailableTargets();
                                                            document.getElementById("configuration_loading_overlay").style.display="none";
                                                        }

                                                        function populateAvailableDistributors() {
                                                            const selectElement = $('#available-distributors');
                                                            selectElement.empty();
                                                            for (let key in distributorsSchema) {
                                                                if (!$(`#distributors-container .card-title:contains(${key})`).length) {
                                                                    selectElement.append(`<option value="${key}">${key}</option>`);
                                                                }
                                                            }
                                                            toggleDistributorSelectContainer();
                                                        }

                                                        function populateAvailableTargets() {
                                                            const selectElement = $('#available-targets');
                                                            selectElement.empty();
                                                            for (let key in targetSchema) {
                                                                if (!$(`#targets-container .card-title:contains(${targetSchema[key].display_name})`).length) {
                                                                    selectElement.append(`<option value="${key}">${targetSchema[key].display_name}</option>`);
                                                                }
                                                            }
                                                            toggleTargetSelectContainer();
                                                        }

                                                        function toggleOperandOption(restriction_id, option, setConfig = false) {
                                                            //alert(restriction_id);
                                                            document.getElementById(restriction_id + '-and-option').classList.remove('operand_selected');
                                                            document.getElementById(restriction_id + '-or-option').classList.remove('operand_selected');
                                                            
                                                            if (option === 'AND') {
                                                                document.getElementById(restriction_id + '-and-option').classList.add('operand_selected');
                                                            } else if (option === 'OR') {
                                                                document.getElementById(restriction_id + '-or-option').classList.add('operand_selected');
                                                            }

                                                            if (setConfig){
                                                                const cc = editor.get();
                                                                setConfigValue(cc, `${restriction_id}-include_operand`, option);
                                                                editor.set(cc);
                                                            }
                                                        }
                                                  
                                                        function update_distributor_active(distid, distributor){
                                                            dist_active = document.getElementById(distid + "-active").checked;
                                                            config = editor.get();
                                                            config.distributors[distributor].active = dist_active;
                                                            editor.set(config);
                                                        }

                                                        function update_target_active(target){
                                                            target_active = document.getElementById(target + "-active").checked;
                                                            config = editor.get();
                                                            config.targets[target].active = target_active;
                                                            editor.set(config);
                                                        }

                                                        function remove_all_cockpit_gunbroker_listings(){
                                                            if (confirm("This will delete all gunbroker listings generated by FFL Cockpit. Do you wish to proceed?")){
                                                                document.getElementById("delete_gunbroker_listings_button").disabled = true;
                                                                document.getElementById('delete_gunbroker_listings_button').innerText = 'Request Sent';
                                                                fetch("https://ffl-api.garidium.com", {
                                                                        method: "POST",
                                                                        headers: {
                                                                            "Accept": "application/json",
                                                                            "Content-Type": "application/json",
                                                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                                                        },
                                                                        body: JSON.stringify({"action": "remove_all_gunbroker_listings", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                                                                    })
                                                                    .then(response=>response.json())
                                                                    .then(data=>{ 
                                                                        alert("Your request for removal of all FFL Cockpit Generated Gunbroker Listings has been submitted. This can take up to an hour if you have many listings. If you do not wish to have FFL Cockpit re-list the product, then you must disable Gunbroker and hit Save.");
                                                                        document.getElementById("delete_gunbroker_listings_button").disabled = false; 
                                                                        document.getElementById('delete_gunbroker_listings_button').innerText = 'Delete all Cokpit-generated Gunbroker Listings';     
                                                                    });
                                                            }
                                                        }

                                                        function addDistributorForm(distributor = "", config = {}) {
                                                            if (distributorsSchema[distributor]){
                                                                const distid = distributorsSchema[distributor].distid;
                                                                let formHtml = `<div class="distcards">
                                                                                    <div class="card mt-2" id="${distid}">
                                                                                        <div class="card-header">
                                                                                            <div class="distid_image_area">
                                                                                                <img src="https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_${distid}.png" alt="${distributor} logo">
                                                                                            </div>
                                                                                            <h5 class="card-title">${distributor}</h5>${distributorsSchema[distributor].automated_fulfillment?"":" <span class='cockpit-watermark'>Product Feed Only</span>"}
                                                                                            <label class="toggle-switch">
                                                                                                <input onchange="update_distributor_active('${distid}', '${distributor}');" type="checkbox" id="${distid}-active" name="${distid}-active" ${config.active ? 'checked' : ''}>
                                                                                                <span class="slider"></span>
                                                                                            </label>
                                                                                        </div>
                                                                                        <div class="card-body">
                                                                                            <div class="action-links">
                                                                                                <span class="configure-link" onclick="viewDistributorDetails('${distributor}', '${distid}')">Configure</span>
                                                                                                <span>|</span>
                                                                                                <span class="remove-link" onclick="confirmRemoveDistributor('${distid}')">Remove</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>`;

                                                                $('#distributors-container').append(formHtml);

                                                                // Remove the selected distributor from the available list
                                                                $(`#available-distributors option[value="${distributor}"]`).remove();

                                                                // Check if all distributors are added
                                                                toggleDistributorSelectContainer();
                                                            }
                                                        }

                                                        function addTargetForm(target = "", config = {}) {
                                                            if (!targetSchema[target]){
                                                                return;
                                                            }
                                                            const target_id = target;
                                                            let formHtml = `<div class="targetcards">
                                                                                <div class="card mt-2" id="${target_id}">
                                                                                    <div class="card-header">
                                                                                        <div class="distid_image_area">
                                                                                            <img src="https://garidium.s3.amazonaws.com/ffl-api/plugin/images/target_logo_${target_id}.png" alt="${target} logo">
                                                                                        </div>
                                                                                        <h5 class="card-title">${targetSchema[target].display_name}</h5>
                                                                                        <label class="toggle-switch">
                                                                                            <input onchange="update_target_active('${target_id}');" type="checkbox" id="${target_id}-active" name="${target_id}-active" ${config.active ? 'checked' : ''}>
                                                                                            <span class="slider"></span>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="action-links">
                                                                                            <span class="configure-link" onclick="viewTargetDetails('${target}', '${target_id}')">Configure</span>
                                                                                            <span>|</span>
                                                                                            <span class="remove-link" onclick="confirmRemoveTarget('${target_id}')">Remove</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>`;

                                                            $('#targets-container').append(formHtml);

                                                            // Remove the selected distributor from the available list
                                                            $(`#available-targets option[value="${target}"]`).remove();

                                                            // Check if all distributors are added
                                                            toggleTargetSelectContainer();
                                                        }

                                                        function confirmRemoveDistributor(distributorId) {
                                                            if (confirm("Are you sure you want to delete this distributor?")) {
                                                                removeDistributor(distributorId);
                                                            }
                                                        }

                                                        function confirmRemoveTarget(targetId) {
                                                            if (confirm("Are you sure you want to delete this target, all existing configuration for this target will be reset.?")) {
                                                                removeTarget(targetId);
                                                            }
                                                        }

                                                        function removeDistributor(distributorId) {
                                                            const distributorName = $(`#${distributorId} .card-title`).text();
                                                            $(`#${distributorId}`).closest('.distcards').remove();

                                                            // Add the removed distributor back to the available list
                                                            $('#available-distributors').append(`<option value="${distributorName}">${distributorName}</option>`);

                                                            // Check if distributor dropdown and button should be shown
                                                            toggleDistributorSelectContainer();

                                                            // Now lets remove it from the actual configuration
                                                            config = editor.get();
                                                            delete config.distributors[distributorName];
                                                            editor.set(config);
                                                        
                                                        }

                                                        function removeTarget(targetId) {
                                                            $(`#${targetId.replace(/\./g, '\\.')}`).closest('.targetcards').remove();


                                                            // Add the removed distributor back to the available list
                                                            $('#available-targets').append(`<option value="${targetId}">${targetSchema[targetId].display_name}</option>`);

                                                            // Check if distributor dropdown and button should be shown
                                                            toggleTargetSelectContainer();

                                                            // Now lets remove it from the actual configuration
                                                            config = editor.get();
                                                            delete config.targets[targetId];
                                                            editor.set(config);
                                                        
                                                        }
                                                        
                                                        function getConfigValue(obj, path) {
                                                            const keys = path.split('-');
                                                            let current = obj;

                                                            for (let key of keys) {
                                                                if (current[key] === undefined) {
                                                                    return undefined;
                                                                }
                                                                current = current[key];
                                                            }

                                                            return current;
                                                        }

                                                        function setConfigValue(obj, path, value) {
                                                            const keys = path.split('-');
                                                            let current = obj;

                                                            for (let i = 0; i < keys.length; i++) {
                                                                const key = keys[i];
                                                                
                                                                // If it's the last key, set the value
                                                                if (i === keys.length - 1) {
                                                                    current[key] = value;
                                                                } else {
                                                                    // If the key doesn't exist, create an empty object
                                                                    if (current[key] === undefined) {
                                                                        current[key] = {};
                                                                    }
                                                                    current = current[key];
                                                                }
                                                            }
                                                        }
                                               
                                                        function viewTargetDetails(target, targetId) {
                                                            const targetDetails = JSON.parse(targetSchema[target].ui_field_schema);
                                                            const displayName = targetSchema[target].display_name;
                                                            $('#detailsModalLabel').text(`${displayName}`);
                                                            const modalBody = $('#modal-body');
                                                            modalBody.empty();
                                                            cc = editor.get();
                                                            
                                                            for (let key in targetDetails) {
                                                                const field = targetDetails[key];
                                                                if (field.helperText){
                                                                    modalBody.append(`
                                                                    <div class="helperDialog">
                                                                        ${field.helperText}
                                                                    </div>
                                                                    `);
                                                                }
                                                                if (field.type == "array") {
                                                                    modalBody.append(`
                                                                        <label for="${field.config_key}">${field.label}:</label>
                                                                        <div class="field-container">
                                                                            <div class="selected-items" id="${field.config_key}"></div>
                                                                            <div class="add-item-container">
                                                                                <span class="add-item" onclick="promptAndAddItems(this, 'email', '${field.config_key}')">Add</span>
                                                                            </div>
                                                                        </div>`);
                                                                    addSelectedItemsToContainer(document.getElementById(field.config_key), getConfigValue(cc, field.config_key));
                                                                } else if (field.type == "rich_text") {
                                                                    modalBody.append(`
                                                                        <div class="textarea-wrapper">
                                                                        <label for="${field.config_key}">${field.label}:</label>
                                                                            <textarea style="height:150px;" id="${field.config_key}" name="${field.config_key}" class="modal-tinymce" data-autosave="true"></textarea>
                                                                        </div>
                                                                    `);
                                                                    
                                                                    tinymce.remove(`#${field.config_key}`);
                                                                    tinymce.init({
                                                                        selector: `#${field.config_key}`,
                                                                        plugins: 'code link lists image',
                                                                        //toolbar: 'code link image | bold italic backcolor forecolor | alignleft aligncenter | numlist bullist',
                                                                        toolbar: 'bold italic backcolor forecolor | alignleft aligncenter | numlist bullist',
                                                                        license_key: 'gpl',
                                                                        menubar: false,
                                                                        branding: false,
                                                                        statusbar: false,
                                                                        setup: function (mceeditor) {
                                                                            mceeditor.on('input', function () {
                                                                                clearTimeout(typingTimer);
                                                                                typingTimer = setTimeout(() => autoSave(mceeditor.getElement()), doneTypingInterval);
                                                                            });

                                                                            mceeditor.on('keydown', function () {
                                                                                clearTimeout(typingTimer);
                                                                            });

                                                                            mceeditor.on('blur', function () {
                                                                                autoSave(mceeditor.getElement());
                                                                            });

                                                                            mceeditor.on('init', function () {
                                                                                // Set the content of TinyMCE after initialization
                                                                                mceeditor.setContent(getConfigValue(cc, field.config_key));
                                                                            });
                                                                        }
                                                                    });                                                 

                                                                } else if (field.type == "boolean") {
                                                                    modalBody.append(`<div class="form-group">
                                                                                        <label for="${field.config_key}">${field.label}</label>
                                                                                        <label class="toggle-switch">
                                                                                            <input type="checkbox" id="${field.config_key}" name="${field.config_key}" ${(field.default && field.default == true) || getConfigValue(cc, field.config_key) == true ? 'checked' : ''} data-autosave="true">
                                                                                            <span class="slider"></span>
                                                                                        </label>
                                                                                    </div>`);
                                                                } else {
                                                                    modalBody.append(`<div class="form-group">
                                                                                        <label for="${field.config_key}">${field.label}</label>
                                                                                        <input type="${field.type}" class="form-control" name="${field.config_key}" id="${field.config_key}" value="${getConfigValue(cc, field.config_key)}" data-autosave="true">
                                                                                    </div>`);
                                                                }
                                                            }

                                                            if (['wikiarms', 'gun.deals', 'ammoseek'].includes(target)) {
                                                                modalBody.append(`
                                                                <div class="helperDialog">
                                                                    Specify <strong>Product Restrictions specific to ${displayName} </strong> in the section below. Expand the section and define which products you want to list on ${target} within each of their product groups.  
                                                                </div>`);
                                                            }else{
                                                                modalBody.append(`
                                                                <div class="helperDialog">
                                                                    Specify <strong>Product Restrictions specific to ${displayName} </strong> in the section below. Expand the section and define which products you want to list on ${target}. By default all products will be listed based on your global product restriction settings, so this is an optional configuration if you need to only show certain products on ${target}.
                                                                </div>`);
                                                            }
                                                            
                                                            modalBody.append(`
                                                                <div class="accordion-header" id="pr_header" style="margin-top:20px;cursor:pointer;" onclick="toggleAccordion('${target}-product-restrictions', this)">
                                                                    <i class="fas fa-plus accordion-toggle-icon"></i>${displayName} Product Restrictions
                                                                </div>
                                                                <div style="margin-top:20px;display:none;" id="${target}-product-restrictions">
                                                                    <div class="other-restrictions-header"><strong>${displayName} Product Restrictions</strong></div>
                                                                    <div id="product-restrictions-container"></div>
                                                            `);

                                                            if (['gunbroker'].includes(target)) {
                                                                modalBody.append(`
                                                                    <button class="btn btn-primary" style="margin-top:50px;" id="delete_gunbroker_listings_button" onclick="remove_all_cockpit_gunbroker_listings()">Delete all FFL Cockpit-Generated Gunbroker Listings</button>
                                                                `);
                                                            }

                                                            // automatically open product restrictions for certain targets
                                                            if (['wikiarms', 'gun.deals', 'ammoseek'].includes(target)) {
                                                                document.getElementById('pr_header').click();
                                                            }
                                                           
                                                            const product_restrictions_container = document.getElementById('product-restrictions-container');
                                                            
                                                            if (['gunbroker', 'woo'].includes(target)) {
                                                                const restrictions = [
                                                                    {"field": "product_class", "modal": "productClassModal", "name": "Product Classes", "select_text": "Select Product Classes"},
                                                                    {"field": "category", "modal": "categoryModal", "name": "Categories", "select_text": "Select Categories"},
                                                                    {"field": "brand", "modal": "brandModal", "name": "Brands", "select_text": "Select Brands"},
                                                                    {"field": "sku", "modal": "prompt", "name": "SKU", "select_text": "Add SKU"},
                                                                    {"field": "upc", "modal": "prompt", "name": "UPC", "select_text": "Add UPC"} 
                                                                ];
                                                                restrictions.forEach(restriction => {
                                                                    const section = document.createElement('div');
                                                                    section.className = 'restriction-section';
                                                                    
                                                                    const header = document.createElement('div');
                                                                    header.className = 'restriction-header';
                                                                    header.innerHTML = `<strong>${restriction.name}</strong>`;
                                                                    section.appendChild(header);

                                                                    const container = document.createElement('div');
                                                                    container.className = 'restriction-container';

                                                                    const includeDiv = document.createElement('div');
                                                                    includeDiv.className = 'restriction-item';
                                                                    
                                                                    var launcher_include = `openModal('${restriction.modal}', 'targets-${target}-product_restrictions-${restriction.field}-include')`;
                                                                    var launcher_exclude = `openModal('${restriction.modal}', 'targets-${target}-product_restrictions-${restriction.field}-exclude')`;
                                                                    if (restriction.modal == "prompt"){
                                                                        launcher_include = `promptAndAddItems(this, '${restriction.field}', 'targets-${target}-product_restrictions-${restriction.field}-include')`;
                                                                        launcher_exclude = `promptAndAddItems(this, '${restriction.field}', 'targets-${target}-product_restrictions-${restriction.field}-exclude')`;
                                                                    }
                                                                    includeDiv.innerHTML = `
                                                                        <label for="product_class">Include - <span style="font-size:9pt !important;">
                                                                            <span id="targets-${target}-product_restrictions-${restriction.field}-and-option" onclick="toggleOperandOption('targets-${target}-product_restrictions-${restriction.field}', 'AND', true)">AND</span> | 
                                                                            <span id="targets-${target}-product_restrictions-${restriction.field}-or-option" onclick="toggleOperandOption('targets-${target}-product_restrictions-${restriction.field}', 'OR', true)">OR</span></span>:
                                                                        </label>
                                                                        <div class="field-container">
                                                                            <div class="selected-items" id="targets-${target}-product_restrictions-${restriction.field}-include"></div>
                                                                            <div class="add-item-container">
                                                                                <span class="add-item" onclick="${launcher_include}">Select</span>&nbsp;|&nbsp;
                                                                                <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('targets-${target}-product_restrictions-${restriction.field}-include'))">Remove All</span>
                                                                            </div>
                                                                        </div>`;
                                                                    container.appendChild(includeDiv);

                                                                    const excludeDiv = document.createElement('div');
                                                                    excludeDiv.className = 'restriction-item';
                                                                    excludeDiv.innerHTML = `
                                                                        <label for="product_class">Exclude:</label>
                                                                        <div class="field-container">
                                                                            <div class="selected-items" id="targets-${target}-product_restrictions-${restriction.field}-exclude"></div>
                                                                            <div class="add-item-container">
                                                                                <span class="add-item" onclick="${launcher_exclude}">Select</span>&nbsp;|&nbsp;
                                                                        <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('targets-${target}-product_restrictions-${restriction.field}-exclude'))">Remove All</span>
                                                                            </div>
                                                                        </div>`;
                                                                    container.appendChild(excludeDiv);
                                                                    section.appendChild(container);
                                                                    product_restrictions_container.appendChild(section);

                                                                    // Now load the data with existing values
                                                                    var config = editor.get();
                                                                    if (config.targets[target].product_restrictions[restriction.field] && config.targets[target].product_restrictions[restriction.field]['include_operand']){
                                                                        toggleOperandOption(`targets-${target}-product_restrictions-${restriction.field}`, config.targets[target].product_restrictions[restriction.field].include_operand);
                                                                    }else{
                                                                        toggleOperandOption(`targets-${target}-product_restrictions-${restriction.field}`, "AND");
                                                                    }
                                                                    const include_excludes = ["include","exclude"];
                                                                    include_excludes.forEach(include_exclude => {
                                                                        const selectedFilterContainer = document.getElementById(`targets-${target}-product_restrictions-${restriction.field}-${include_exclude}`);
                                                                        if (selectedFilterContainer != null) {
                                                                            selectedFilterContainer.replaceChildren();
                                                                            if (config.targets?.[target]?.product_restrictions?.[restriction.field]?.[include_exclude]) {
                                                                                addSelectedItemsToContainer(selectedFilterContainer, config.targets[target].product_restrictions[restriction.field][include_exclude]);
                                                                            }
                                                                        }
                                                                    });
                                                                });
                                                            } else if (['gun.deals', 'wikiarms', 'ammoseek'].includes(target)) {
                                                                var rss_field_categories = {
                                                                    "wikiarms": ["guns","brass","powder","bullets","primers","magazines","ammunition","reloading_misc"],
                                                                    "ammoseek": ["guns","brass","powder","bullets","primers","magazines","ammunition"],
                                                                    "gun.deals": ["guns","brass","other","parts","bullets","primers","reloading","ammunition"]
                                                                }
                                                                const restrictions = [
                                                                    {"field": "category", "modal": "categoryModal", "name": "Categories", "select_text": "Select Categories"},
                                                                    {"field": "sku", "modal": "prompt", "name": "SKU", "select_text": "Add SKU"}
                                                                ];
                                                                
                                                                rss_field_categories[target].forEach(product_type => {
                                                                    const section = document.createElement('div');
                                                                    section.className = 'restriction-section';
                                                                    
                                                                    const header = document.createElement('div');
                                                                    header.className = 'restriction-header';
                                                                    header.innerHTML = `<strong>${product_type}</strong>`;
                                                                    section.appendChild(header);

                                                                    const container = document.createElement('div');
                                                                    container.className = 'restriction-container';

                                                                    const categoryDiv = document.createElement('div');
                                                                    categoryDiv.className = 'restriction-item';
                                                                    
                                                                    var launcher_category = `openModal('categoryModal', 'targets-${target}-listed_products-${product_type}-category-include')`;
                                                                    var launcher_sku = `promptAndAddItems(this, 'sku', 'targets-${target}-listed_products-${product_type}-sku-include')`;
                                                                        
                                                                    categoryDiv.innerHTML = `
                                                                        <label for="product_class">Categories:</label>
                                                                        <div class="field-container">
                                                                            <div class="selected-items" id="targets-${target}-listed_products-${product_type}-category-include"></div>
                                                                            <div class="add-item-container">
                                                                                <span class="add-item" onclick="${launcher_category}">Select</span>&nbsp;|&nbsp;
                                                                        <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('targets-${target}-listed_products-${product_type}-category-include'))">Remove All</span>
                                                                            </div>
                                                                        </div>`;
                                                                    container.appendChild(categoryDiv);

                                                                    const skuDiv = document.createElement('div');
                                                                    skuDiv.className = 'restriction-item';
                                                                    skuDiv.innerHTML = `
                                                                        <label for="product_class">SKU's:</label>
                                                                        <div class="field-container">
                                                                            <div class="selected-items" id="targets-${target}-listed_products-${product_type}-sku-include"></div>
                                                                            <div class="add-item-container">
                                                                                <span class="add-item" onclick="${launcher_sku}">Select</span>&nbsp;|&nbsp;
                                                                        <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('targets-${target}-listed_products-${product_type}-sku-include'))">Remove All</span>
                                                                            </div>
                                                                        </div>`;
                                                                    container.appendChild(skuDiv);

                                                                    section.appendChild(container);
                                                                    product_restrictions_container.appendChild(section);

                                                                    // Now load the data with existing values
                                                                    const restrictions_types = ["category","sku"];
                                                                    var config = editor.get();
                                                                    restrictions_types.forEach(restriction_type => {
                                                                        const selectedFilterContainer = document.getElementById(`targets-${target}-listed_products-${product_type}-${restriction_type}-include`);
                                                                        if (selectedFilterContainer != null) {
                                                                            selectedFilterContainer.replaceChildren();
                                                                            if (config.targets?.[target]?.listed_products?.[product_type]?.[restriction_type]?.['include']) {
                                                                                addSelectedItemsToContainer(selectedFilterContainer, config.targets[target].listed_products[product_type][restriction_type]['include']);
                                                                            }
                                                                        }
                                                                    });
                                                                  
                                                                });


                                                            }
                                                            setupAutoSave();
                                                            $('#detailsModal').modal('show');
                                                        }

                                                        function viewDistributorDetails(distributor, distributorId) {
                                                            const distributorDetails = JSON.parse(distributorsSchema[distributor].ui_field_schema);
                                                            $('#detailsModalLabel').text(`${distributor}`);
                                                            const modalBody = $('#modal-body');
                                                            modalBody.empty();
                                                            cc = editor.get();
                                                            
                                                            for (let key in distributorDetails) {
                                                                const field = distributorDetails[key];
                                                                const field_key = "distributors-" + distributor + "-" + field.config_key;
                                                                if (field.helperText){
                                                                    modalBody.append(`
                                                                    <div class="helperDialog">
                                                                        ${field.helperText}
                                                                    </div>
                                                                    `);
                                                                }
                                                                if (field.type == "array"){
                                                                    modalBody.append(`
                                                                    <label for="${field_key}">${field.label}:</label>
                                                                    <div class="field-container" style="padding-bottom:5px;margin-bottom:20px;border-bottom:solid #ccc 1px;">
                                                                        <div class="selected-items" id="${field_key}"></div>
                                                                        <div class="add-item-container">
                                                                            <span class="add-item" onclick="promptAndAddItems(this, 'email', '${field_key}')">Add Email</span>
                                                                        </div>
                                                                    </div>`);
                                                                    addSelectedItemsToContainer(document.getElementById(field_key), getConfigValue(cc, field_key));

                                                                } else if (field.type == "state-selector"){
                                                                    modalBody.append(`
                                                                        <div class="form-group">
                                                                            <label for="${field_key}">${field.label}</label>
                                                                            <select class="form-control" name="${field_key}" id="${field_key}" data-autosave="true"></select>
                                                                        </div>
                                                                    `);

                                                                    const selectedState = getConfigValue(cc, field_key);
                                                                    const dropdown = document.getElementById(field_key);
                                                                    statesAndTerritories.forEach(state => {
                                                                        const option = document.createElement('option');
                                                                        option.value = state.value;
                                                                        option.textContent = state.text;
                                                                        if (selectedState == state.value) {
                                                                            option.selected = true;
                                                                        }
                                                                        dropdown.appendChild(option);
                                                                    });

                                                                    addSelectedItemsToContainer(document.getElementById(field_key), getConfigValue(cc, field_key));

                                                                }else{
                                                                    modalBody.append(`<div class="form-group">
                                                                                        <label for="${field_key}">${field.label}</label>
                                                                                        <input type="${field.type}" class="form-control" name="${field_key}" id="${field_key}" value="${getConfigValue(cc, field_key)}" data-autosave="true">
                                                                                    </div>`);
                                                                }
        
                                                            }

                                                            modalBody.append(`
                                                                <div class="helperDialog">
                                                                    Specify <strong>Product Restrictions specific to ${distributor} </strong> in the section below. Expand the section and define which products you want to pull in from ${distributor}. By default all products will come through based on your global product restriction settings, so this is an optional configuration if you need to only pull certain products from ${distributor}.
                                                                </div>
                                                                <div class="accordion-header" style="margin-top:20px;cursor:pointer;" onclick="toggleAccordion('${distributor}-product-restrictions', this)">
                                                                    <i class="fas fa-plus accordion-toggle-icon"></i>${distributor} Product Restrictions
                                                                </div>
                                                                <div style="margin-top:20px;display:none;" id="${distributor}-product-restrictions">
                                                                    <div class="other-restrictions-header"><strong>${distributor} Product Restrictions</strong></div>
                                                                    <div style="margin-top:20px;" class="restriction-section">
                                                                        <div class="other-restrictions-header">List only products eligible for dropshipping</strong></div>
                                                                        <div class="helperDialog">
                                                                            If this is toggled on, only products elgible for dropshipping will be listed. If it's off, all items from the Distributor (whether or not they are elgible for dropshipping) will be listed
                                                                        </div>
                                                                        <div style="width:!00%;text-align:center;">
                                                                            <label for="sell_at_map">List only products eligible for dropshipping:&nbsp;</label>
                                                                            <label class="toggle-switch">
                                                                                <input type="checkbox" id="distributors-${distributor}-drop_ship_only_items" name="distributors-${distributor}-drop_ship_only_items" ${getConfigValue(cc, "distributors-" + distributor + "-drop_ship_only_items") ? 'checked' : ''}  data-autosave="true">
                                                                                <span class="slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div style="margin-top:20px;" class="restriction-section">
                                                                        <div class="other-restrictions-header">Product Cost Retrictions</strong></div>
                                                                        <div class="helperDialog">
                                                                            Some distributors have handling fees for orders below a certain amount. These cost controls give you the ability to set a minimum cost for items listed on your site from ${distributor}
                                                                        </div>
                                                                        <div class="cost-restrictions">
                                                                            <div class="cost-field">
                                                                                <label for="distributors-${distributor}-product_restrictions-cost-global_restrictions-min_distributor_cost">Min Cost:&nbsp;</label>
                                                                                <input class="form-control" type="number" id="distributors-${distributor}-product_restrictions-cost-global_restrictions-min_distributor_cost" name="distributors-${distributor}-product_restrictions-cost-global_restrictions-min_distributor_cost" data-autosave="true" value="${getConfigValue(cc, "distributors-" + distributor + "-product_restrictions-cost-global_restrictions-min_distributor_cost")}">
                                                                            </div>
                                                                            <div class="cost-field"> 
                                                                                <label for="distributors-${distributor}-product_restrictions-cost-global_restrictions-max_distributor_cost">Max Cost:</label>
                                                                                <input class="form-control" type="number" id="distributors-${distributor}-product_restrictions-cost-global_restrictions-max_distributor_cost" name="distributors-${distributor}-product_restrictions-cost-global_restrictions-max_distributor_cost" data-autosave="true" value="${getConfigValue(cc, "distributors-" + distributor + "-product_restrictions-cost-global_restrictions-max_distributor_cost")}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div id="product-restrictions-container"></div>
                                                            `);
                                                           
                                                            const product_restrictions_container = document.getElementById('product-restrictions-container');
                                                            const restrictions = [
                                                                {"field": "product_class", "modal": "productClassModal", "name": "Product Classes", "select_text": "Select Product Classes"},
                                                                {"field": "category", "modal": "categoryModal", "name": "Categories", "select_text": "Select Categories"},
                                                                {"field": "brand", "modal": "brandModal", "name": "Brands", "select_text": "Select Brands"},
                                                                {"field": "sku", "modal": "prompt", "name": "SKU", "select_text": "Add SKU"},
                                                                {"field": "upc", "modal": "prompt", "name": "UPC", "select_text": "Add UPC"} 
                                                            ];

                                                                
                                                            restrictions.forEach(restriction => {
                                                                const section = document.createElement('div');
                                                                section.className = 'restriction-section';
                                                                
                                                                const header = document.createElement('div');
                                                                header.className = 'restriction-header';
                                                                header.innerHTML = `<strong>${restriction.name}</strong>`;
                                                                section.appendChild(header);

                                                                const container = document.createElement('div');
                                                                container.className = 'restriction-container';

                                                                const includeDiv = document.createElement('div');
                                                                includeDiv.className = 'restriction-item';
                                                                
                                                                var launcher_include = `openModal('${restriction.modal}', 'distributors-${distributor}-product_restrictions-${restriction.field}-include')`;
                                                                var launcher_exclude = `openModal('${restriction.modal}', 'distributors-${distributor}-product_restrictions-${restriction.field}-exclude')`;
                                                                if (restriction.modal == "prompt"){
                                                                    launcher_include = `promptAndAddItems(this, '${restriction.field}', 'distributors-${distributor}-product_restrictions-${restriction.field}-include')`;
                                                                    launcher_exclude = `promptAndAddItems(this, '${restriction.field}', 'distributors-${distributor}-product_restrictions-${restriction.field}-exclude')`;
                                                                }
                                                                includeDiv.innerHTML = `
                                                                    <label for="product_class">Include - <span style="font-size:9pt !important;">
                                                                        <span id="distributors-${distributor}-product_restrictions-${restriction.field}-and-option" onclick="toggleOperandOption('distributors-${distributor}-product_restrictions-${restriction.field}', 'AND', true)">AND</span> | 
                                                                        <span id="distributors-${distributor}-product_restrictions-${restriction.field}-or-option" onclick="toggleOperandOption('distributors-${distributor}-product_restrictions-${restriction.field}', 'OR', true)">OR</span></span>:
                                                                    </label>
                                                                    <div class="field-container">
                                                                        <div class="selected-items" id="distributors-${distributor}-product_restrictions-${restriction.field}-include"></div>
                                                                        <div class="add-item-container">
                                                                            <span class="add-item" onclick="${launcher_include}">Select</span>&nbsp;|&nbsp;
                                                                            <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('distributors-${distributor}-product_restrictions-${restriction.field}-include'))">Remove All</span>
                                                                        </div>
                                                                    </div>`;
                                                                container.appendChild(includeDiv);

                                                                const excludeDiv = document.createElement('div');
                                                                excludeDiv.className = 'restriction-item';
                                                                excludeDiv.innerHTML = `
                                                                    <label for="product_class">Exclude:</label>
                                                                    <div class="field-container">
                                                                        <div class="selected-items" id="distributors-${distributor}-product_restrictions-${restriction.field}-exclude"></div>
                                                                        <div class="add-item-container">
                                                                            <span class="add-item" onclick="${launcher_exclude}">Select</span>&nbsp;|&nbsp;
                                                                            <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('distributors-${distributor}-product_restrictions-${restriction.field}-exclude'))">Remove All</span>
                                                                        </div>
                                                                    </div>`;
                                                                container.appendChild(excludeDiv);
                                                                section.appendChild(container);
                                                                product_restrictions_container.appendChild(section);

                                                                // Now load the data with existing values
                                                                var config = editor.get();
                                                                if (config.distributors[distributor].product_restrictions[restriction.field] && config.distributors[distributor].product_restrictions[restriction.field]['include_operand']){
                                                                    toggleOperandOption(`distributors-${distributor}-product_restrictions-${restriction.field}`, config.distributors[distributor].product_restrictions[restriction.field].include_operand);
                                                                }else{
                                                                    toggleOperandOption(`distributors-${distributor}-product_restrictions-${restriction.field}`, "AND");
                                                                }
                                                                
                                                                const include_excludes = ["include","exclude"];
                                                                include_excludes.forEach(include_exclude => {
                                                                    const selectedFilterContainer = document.getElementById(`distributors-${distributor}-product_restrictions-${restriction.field}-${include_exclude}`);
                                                                    if (selectedFilterContainer != null) {
                                                                        selectedFilterContainer.replaceChildren();
                                                                        if (config.distributors?.[distributor]?.product_restrictions?.[restriction.field]?.[include_exclude]) {
                                                                            addSelectedItemsToContainer(selectedFilterContainer, config.distributors[distributor].product_restrictions[restriction.field][include_exclude]);
                                                                        }
                                                                    }
                                                                });
                                                                
                                                            });
                                                            
                                                            setupAutoSave();
                                                            $('#detailsModal').modal('show');
                                                        }

                                                        function toggleDistributorSelectContainer() {
                                                            const selectContainer = $('#distributor-select-container');
                                                            if ($('#available-distributors option').length === 0) {
                                                                selectContainer.hide();
                                                            } else {
                                                                selectContainer.show();
                                                            }
                                                        }

                                                        function toggleTargetSelectContainer() {
                                                            const selectContainer = $('#target-select-container');
                                                            if ($('#available-targets option').length === 0) {
                                                                selectContainer.hide();
                                                            } else {
                                                                selectContainer.show();
                                                            }
                                                        }
                                                    </script>
                                            </div>

                                            <div class="tab-pane fade" id="product-restrictions" role="tabpanel" aria-labelledby="product-restrictions-tab">
                                                <!-- Product Restrictions Form Content -->

                                                <div class="main-container">
                                                    <div class="left-container">
                                                        <div class="other-restrictions-header"><strong>Product Restrictions</strong></div>
                                                        <div class="helperDialog">
                                                            This section allows you to configure the products you would like included on your site.
                                                            <uL style="list-style-type: disc;">
                                                                <li>All products from Distributors are included by default. If you do nothing below, all products we be included. We do not suggest that, and instead be selective about the product listed. Use either the product class selections or the categories to select the items you want to "include". We suggest using categories, so you can be more selective.</li>
                                                                <li><span style="color:red;"><b>**Important:</b> If you add items to the "include" sections, those items selected will be the <u>ONLY</u> product listed on your site.</span> For example, If you add a single UPC to the includes, your site will only have that single product listed.</li>
                                                                <li>If you have items in the "include" section, there is no need to have items in the "exclude" section, since only those items in the "include" will be included.</li>
                                                                <li>Reach out to support@garidium.com if you have any questions.</li>
                                                            </ul>
                                                        </div>
                                                        <div class="restriction-section">
                                                            <div class="restriction-header"><strong>Cost-based Restrictions</strong></div>
                                                            <div class="pricing-assumptions-form-row">
                                                                <label for="product_restrictions-cost-global_restrictions-min_distributor_cost">Min Distributor Cost ($):</label>
                                                                <input type="number" id="product_restrictions-cost-global_restrictions-min_distributor_cost" name="product_restrictions-cost-global_restrictions-min_distributor_cost" data-autosave="true">
                                                            </div>
                                                            <div class="pricing-assumptions-form-row">
                                                                <label for="product_restrictions-cost-global_restrictions-max_distributor_cost">Max Distributor Cost ($):</label>
                                                                <input type="number" id="product_restrictions-cost-global_restrictions-max_distributor_cost" name="product_restrictions-cost-global_restrictions-max_distributor_cost" data-autosave="true">
                                                                
                                                            </div>
                                                            <div class="helperDialog" style="text-align:center;">Additional cost-based restriction options available in advanced tab</div>
                                                        </div>
                                                        <div class="restriction-section">
                                                            <div class="restriction-header"><strong>Quantity-based Restrictions</strong></div>
                                                            <div class="pricing-assumptions-form-row">
                                                                <label for="product_restrictions-min_quantity_to_list">Min Distributor Quantity to List:</label>
                                                                <input type="number" id="product_restrictions-min_quantity_to_list" name="product_restrictions-min_quantity_to_list" data-autosave="true">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="right-container">
                                                        <div id="product-restrictions-container"></div>
                                                    </div>
                                                </div>
                                                <script>
                                                    const product_restrictions_container = document.getElementById('product-restrictions-container');
                                                    const restrictions = [
                                                        {"field": "product_class", "modal": "productClassModal", "name": "Product Classes", "select_text": "Select Product Classes"},
                                                        {"field": "category", "modal": "categoryModal", "name": "Categories", "select_text": "Select Categories"},
                                                        {"field": "brand", "modal": "brandModal", "name": "Brands", "select_text": "Select Brands"},
                                                        {"field": "sku", "modal": "prompt", "name": "SKU", "select_text": "Add SKU"},
                                                        {"field": "upc", "modal": "prompt", "name": "UPC", "select_text": "Add UPC"} 
                                                    ];

                                                    restrictions.forEach(restriction => {
                                                        const section = document.createElement('div');
                                                        section.className = 'restriction-section';
                                                        
                                                        const header = document.createElement('div');
                                                        header.className = 'restriction-header';
                                                        header.innerHTML = `<strong>${restriction.name}</strong>`;
                                                        section.appendChild(header);

                                                        const container = document.createElement('div');
                                                        container.className = 'restriction-container';

                                                        const includeDiv = document.createElement('div');
                                                        includeDiv.className = 'restriction-item';
                                                        
                                                        var launcher_include = `openModal('${restriction.modal}', 'product_restrictions-${restriction.field}-include')`;
                                                        var launcher_exclude = `openModal('${restriction.modal}', 'product_restrictions-${restriction.field}-exclude')`;
                                                        if (restriction.modal == "prompt"){
                                                            launcher_include = `promptAndAddItems(this, '${restriction.field}', 'product_restrictions-${restriction.field}-include')`;
                                                            launcher_exclude = `promptAndAddItems(this, '${restriction.field}', 'product_restrictions-${restriction.field}-exclude')`;
                                                        }
                                                        
                                                        includeDiv.innerHTML = `
                                                            <label for="product_class"><span style="color:red">**</span>Include - <span style="font-size:9pt !important;">
                                                                <span id="product_restrictions-${restriction.field}-and-option" onclick="toggleOperandOption('product_restrictions-${restriction.field}', 'AND', true)">AND</span> | 
                                                                <span id="product_restrictions-${restriction.field}-or-option" onclick="toggleOperandOption('product_restrictions-${restriction.field}', 'OR', true)">OR</span></span>:
                                                            </label>
                                                            <div class="field-container">
                                                                <div class="selected-items" id="product_restrictions-${restriction.field}-include"></div>
                                                                <div class="add-item-container">
                                                                    <span class="add-item" onclick="${launcher_include}">Select</span>&nbsp;|&nbsp;
                                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('product_restrictions-${restriction.field}-include'))">Remove All</span>
                                                                </div>
                                                            </div>`;
                                                        container.appendChild(includeDiv);

                                                        const excludeDiv = document.createElement('div');
                                                        excludeDiv.className = 'restriction-item';
                                                        excludeDiv.innerHTML = `
                                                            <label for="product_class">Exclude:</label>
                                                            <div class="field-container">
                                                                <div class="selected-items" id="product_restrictions-${restriction.field}-exclude"></div>
                                                                <div class="add-item-container">
                                                                    <span class="add-item" onclick="${launcher_exclude}">Select</span>&nbsp;|&nbsp;
                                                                    <span class="add-item" onclick="removeAllConfigArrayItems(document.getElementById('product_restrictions-${restriction.field}-exclude'))">Remove All</span>
                                                                </div>
                                                            </div>`;
                                                        container.appendChild(excludeDiv);
                                                        section.appendChild(container);
                                                        product_restrictions_container.appendChild(section);
                                                    });
                                                </script>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="classic-configurator" role="tabpanel" aria-labelledby="classic-configurator-tab">
                                                <!-- Advanced Configuration Content -->
                                                <div class="helperDialog"><strong>Advanced Configuration</strong> is where the magic happens. All changes you make with the fancy UI (User Interface) in the other tabs, ultimately change what's set in here. If your brave, you can decide to only use the Advanced Tab. Feel free to swittch the mode to Text as well to enter the wonderful word of JSON editing. This configuration panel includes some options not available in other tabs. It will also highlight any errors that may exist within your current configuration.</div>
                                                <div id="jsoneditor" style="width: 100%; height: 500px;margin-top:20px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="g_ffl_cockpit_configuration" id="g_ffl_cockpit_configuration">
                                    <script>
                                        function get_and_set_cockpit_configuration(api_key, isAdminRequest){
                                            try {
                                                if (api_key != null && api_key.length > 0){
                                                    var admin_key = api_key;
                                                    if (isAdminRequest){
                                                        admin_key = "<?php echo esc_attr($gFFLCockpitKey);?>";
                                                    }
                                                    fetch("https://ffl-api.garidium.com", {
                                                        method: "POST",
                                                        headers: {
                                                        "Accept": "application/json",
                                                        "Content-Type": "application/json",
                                                        "x-api-key": admin_key,
                                                        },
                                                        body: JSON.stringify({"action": "get_subscription", "data": {"api_key": api_key}})
                                                    })
                                                    .then(response=>response.json())
                                                    .then(data=>{
                                                        try{
                                                            console.log(">Retreived Configuration");
                                                            cockpit_configuration = JSON.parse(data[0].cockpit_configuration);
                                                            console.log(">Parsed Configuration");
                                                            editor.set(cockpit_configuration);
                                                            console.log(">Set Configuration");
                                                            initialCockpitConfiguration = editor.get();
                                                            console.log(">Initialied initial Configuration");
                                                            load_fancy_editor(cockpit_configuration);
                                                            console.log(">Loaded Fancy Editor");
                                                            setupAutoSave();
                                                            console.log(">AutoSave Setup");
                                                        } catch (error) {
                                                            alert("No configuration found for this key, setting to default.");
                                                        }
                                                    });
                                                }else{
                                                    alert("No API Key Configured");
                                                }
                                            }catch(error){
                                                alert("Please validate key, no configuration was found");
                                                console.log(error);
                                            }
                                        }
                                    </script>
                                    <script>
                                        var editor = new JSONEditor(document.getElementById("jsoneditor"));
                                        editor.set({"Loading Configuration": "Please wait..."});
                                        $(document).ready(function () {
                                            
                                            async function initialize() {
                                                var tschema = await get_target_schema();
                                                if (tschema == null || gFFLCockpitKey == null || gFFLCockpitKey == undefined || gFFLCockpitKey.length < 40){
                                                    document.getElementById('configuration').innerHTML='<b>There was a problem retreiving your FFL Cockpit configuration</b>. If you have not purchased a key for FFL Cockpit, you can do so here: <a target=_blank href="https://garidium.com/product/ffl-cockpit-checkout-bundle/">Purchase FFL Cockpit Key</a>. If you have a key, and believe you entered it properly below, please contact support@garidium.com for assistance. This error can also appear if there was a temporary system outage. In that case please check back later.';
                                                    document.getElementById('configuration').style.background = "#e7bec5";
                                                    document.getElementById('configuration').style.margin = "20px";
                                                    document.getElementById('configuration').style.border = "solid black 2px";
                                                    document.getElementById('cockpit_main_tab_control').style.display='none';
                                                    return;
                                                }

                                                await get_distributors_schema();
                                                fetch("https://ffl-api.garidium.com", {
                                                    method: "POST",
                                                    headers: {
                                                        "Accept": "application/json",
                                                        "Content-Type": "application/json",
                                                        "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>",
                                                    },
                                                    body: JSON.stringify({
                                                        "action": "get_configuration_schema",
                                                        "data": {
                                                            "api_key": "<?php echo esc_attr($gFFLCockpitKey);?>"
                                                        }
                                                    })
                                                })
                                                .then(response => response.json())
                                                .then(data => { 
                                                    if (data != null) {
                                                        build_grid(data.schema);
                                                        $('#add-distributor').on('click', function () {
                                                                const selectedDistributor = $('#available-distributors').val();
                                                                if (selectedDistributor) {
                                                                    addDistributorForm(selectedDistributor, {});

                                                                    // Now add the distributor info to the configuration
                                                                    config = editor.get();
                                                                    config.distributors[selectedDistributor] = JSON.parse(distributorsSchema[selectedDistributor]['default_config']);
                                                                    editor.set(config);
                                                                }
                                                            });

                                                        $('#add-target').on('click', function () {
                                                            const selectedTarget = $('#available-targets').val();
                                                            if (selectedTarget) {
                                                                addTargetForm(selectedTarget, {});

                                                                // Now add the distributor info to the configuration
                                                                config = editor.get();
                                                                config.targets[selectedTarget] = JSON.parse(targetSchema[selectedTarget]['default_config']);
                                                                editor.set(config);
                                                            }
                                                        });
                                                        return true;
                                                    }
                                                });
                                                return false;
                                            }
                                            initialize();
                                        });

                                        $(document).on('mouseup', function (e) { 
                                            if (e.target.id == "detailsModal") { 
                                                $('#detailsModal').modal('hide');
                                            } 
                                        });

                                        function getValidationErrorMessage(errors) {
                                            let messages = [];
                                            if (Array.isArray(errors)) {
                                                errors.forEach(error => {
                                                    if (error.message) {
                                                        messages.push({"dataPath": error.dataPath, "message": error.message, "data": error.data ? `(${error.data}) ` : ""});
                                                    } else if (error.error) {
                                                        messages.push({"dataPath": error.error.dataPath, "message": error.error.message, "data": error.error.data ? `(${error.error.data}) ` : ""});
                                                    } else if (error.errors) {
                                                        error.errors.forEach(innerError => {
                                                            if (innerError.message) {
                                                                messages.push({"dataPath": innerError.dataPath, "message": innerError.message, "data": innerError.data ? `(${innerError.data}) ` : ""});
                                                            }
                                                        });
                                                    }
                                                });
                                            } else if (typeof errors === 'object' && errors !== null) {
                                                if (errors.message) {
                                                    messages.push({"dataPath": errors.dataPath, "message": errors.message, "data": errors.data ? `(${errors.data}) ` : ""});
                                                } else if (errors.errors) {
                                                    errors.errors.forEach(innerError => {
                                                        if (innerError.message) {
                                                            messages.push({"dataPath": innerError.dataPath, "message": innerError.message, "data": innerError.data ? `(${innerError.data}) ` : ""});
                                                        }
                                                    });
                                                }
                                            }

                                            if (messages.length === 0) {
                                                messages.push('An unknown error occurred.');
                                            }

                                            // Display messages to the user
                                            let returnMessage = "";
                                            messages.forEach(message => {
                                                let errorMessage = message.message;
                                                if (message.message.includes("1791")) {
                                                    errorMessage = "One or more of your selected brands " + message.data + " is invalid, or has been removed and consolidated with another brand name.";
                                                } else if (message.message.includes("AR Rifles")) {
                                                    errorMessage = "One of your selected categories " + message.data + " is invalid.";
                                                } else {
                                                    errorMessage = message.data + errorMessage;
                                                }
                                                returnMessage += `<li>${message.dataPath} - ${errorMessage}</li>`;
                                            });
                                            return returnMessage;
                                        }
``

                                        function build_grid(config_schema){
                                            const onChangeHandler = function () {
                                                const currentJson = editor.get();
                                                const isChanged = initialCockpitConfiguration !=null && JSON.stringify(currentJson) !== JSON.stringify(initialCockpitConfiguration);
                                                document.getElementById('unsaved-indicator').style.display = isChanged ? 'block' : 'none';
                                                const errors = editor.validate();
                                                // Assume 'editor' is your jsoneditor instance
                                                editor.validate().then((results) => {
                                                    const errorContainer = document.getElementById("validation-errors");
                                                    if (results.length === 0) {
                                                        errorContainer.innerHTML = '';
                                                    } else {
                                                        let errorMessages = 'There are <span style="color:red;text-decoration:underline;">' + results.length + ' errors detected</span> in your configuration. Please review and resolve these errors or you will not be able to save your changes.<br><br><span style="margin-left:5px;color:red;font-weight:bold;">Errors:</span><ul style="margin-top:5px;margin-bottom:0px;padding-bottom:0px;color:red;padding-left: 30px;list-style-type: disc;">';
                                                        errorMessages += getValidationErrorMessage(results);
                                                        errorMessages += '</ul>';
                                                        errorContainer.innerHTML = errorMessages;
                                                    }
                                                }).catch((err) => {
                                                    console.error("Validation failed:", err);
                                                });

                                            };

                                            const options = {
                                                modes: ['text','tree'],
                                                mode: 'tree',
                                                ace: ace,
                                                schema: config_schema,
                                                onChange: onChangeHandler
                                            };
                                            editor.destroy();
                                            editor = new JSONEditor(document.getElementById("jsoneditor"), options);
                                           
                                            editor.set({"Loading Configuration": "Please wait..."});
                                            get_and_set_cockpit_configuration("<?php echo esc_attr($gFFLCockpitKey);?>", false);
                                
                                            // Override the set function to include onChangeHandler
                                            const originalSet = editor.set.bind(editor);
                                            editor.set = function (json) {
                                                originalSet(json);
                                                onChangeHandler();
                                            };

                                            if (window.location.host == 'garidium.com' || window.location.host == 'localhost:8000'){
                                                document.getElementById('g-ffl-admin-buttons').style.display = '';
                                            }

                                            // Warn user before leaving the page with unsaved changes
                                            window.addEventListener('beforeunload', function (e) {
                                                if (initialCockpitConfiguration!=null && JSON.stringify(editor.get()) !== JSON.stringify(initialCockpitConfiguration)) {
                                                    const confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
                                                    e.returnValue = confirmationMessage; // Gecko, Trident, Chrome 34+
                                                    return confirmationMessage; // Gecko, WebKit, Chrome <34
                                                }
                                            });

                                            
                                        }
                                    </script>
                                </td>
                            </tr>
                            <tr valign="top" id="white_label_settings_name" style="display:none;">
                                <th scope="row">Plugin Name:</th>
                                <td>
                                    <input type="text" style="width: 30%" name="g_ffl_cockpit_plugin_name" maxlength=20
                                            value="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_name') != '' ? get_option('g_ffl_cockpit_plugin_name') : 'g-FFL Cockpit'); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top" id="white_label_settings_url" style="display:none;">
                                <th scope="row">Plugin Logo URL:</th>
                                <td>
                                    <input type="text" style="width: 500px;" name="g_ffl_cockpit_plugin_logo_url"
                                            value="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>"/>
                                </td>
                            </tr>
                        </table>
                        <a style="cursor:pointer;" onclick="document.getElementById('white_label_settings_name').style.display='';document.getElementById('white_label_settings_url').style.display='';">&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;</a>
                </div>
                <table style="width:100%;">
                            <tr>
                                <td>
                                    <div>
                                        <button class="btn btn-primary" id="save_cockpit_configuration_button">Save Changes</button>
                                        <script type="text/javascript">
                                            document.getElementById("save_cockpit_configuration_button").addEventListener("click", function(){
                                                document.getElementById("save_cockpit_configuration_button").disabled = true;
                                                document.getElementById('save_cockpit_configuration_button').innerText = 'Please Wait...';
                                                event.preventDefault();
                                                if (setConfig(gFFLCockpitKey)){
                                                    refreshEditor();
                                                }
                                                document.getElementById("save_cockpit_configuration_button").disabled = false;
                                                document.getElementById('save_cockpit_configuration_button').innerText = 'Save Changes';
                                            });
                                        </script>
                                    </div>
                                </td>
                                <td align="right">
                                    <div>
                                    <button class="btn btn-primary" id="validate_configuration_button">Validate Configuration</button>
                                        <script>
                                            document.getElementById("validate_configuration_button").addEventListener("click", function(){
                                            document.getElementById("validate_configuration_button").disabled = true;
                                            document.getElementById('validate_configuration_button').innerText = 'Request Sent';
                                            fetch("https://ffl-api.garidium.com", {
                                                    method: "POST",
                                                    headers: {
                                                    "Accept": "application/json",
                                                    "Content-Type": "application/json",
                                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                                    },
                                                    body: JSON.stringify({"action": "validate_cockpit_configuration", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                                                })
                                                .then(response=>response.json())
                                                .then(data=>{ 
                                                    alert("Your request for validation was submitted. You should receive an email within 5-minutes summarizing any critical configurations issues (if you have any).");
                                                    document.getElementById("validate_configuration_button").disabled = false; 
                                                    document.getElementById('validate_configuration_button').innerText = 'Validate Configuration';     
                                                });
                                            });
                                        </script>

                                        <button class="btn btn-primary" id="send_test_fulfillment_emails_button">Send Test Fulfillment Emails</button>
                                        <script>
                                            document.getElementById("send_test_fulfillment_emails_button").addEventListener("click", function(){
                                                document.getElementById("send_test_fulfillment_emails_button").disabled = true;
                                                document.getElementById('send_test_fulfillment_emails_button').innerText = 'Please Wait...';
                                                fetch("https://ffl-api.garidium.com", {
                                                        method: "POST",
                                                        headers: {
                                                        "Accept": "application/json",
                                                        "Content-Type": "application/json",
                                                        "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                                        },
                                                        body: JSON.stringify({"action": "send_test_emails", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                                                    })
                                                    .then(response=>response.json())
                                                    .then(data=>{ 
                                                        document.getElementById("send_test_fulfillment_emails_button").disabled = false; 
                                                        document.getElementById('send_test_fulfillment_emails_button').innerText = 'Send Test Fulfillment Emails';     
                                                    });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        </table>
            </div>
            <div id="product_feed" class="tabcontent">
                <!-- The Modal -->
                <div id="productDetailsModal" class="cockpit-modal">
                    <!-- Modal content -->
                    <div class="cockpit-modal-content modal-content">
                        <span id="productDetailImageModalCloser" class="close">&times;</span>
                        <div align="center" id="product_detail_div"></div>
                    </div>
                    <script>
                        // Get the modal
                        var modal = document.getElementById("productDetailsModal");

                        // Get the button that opens the modal
                        var btn = document.getElementById("myBtn");

                        // Get the <span> element that closes the modal
                        var span = document.getElementById("productDetailImageModalCloser");

                        function load_product_data(title, distributor, sku, img_url){
                            modal.style.display = "block";
                            document.getElementById("product_detail_div").innerHTML = "<h3>" + title + "</h3><br><img class='responsive-image' src='" + img_url + "'/><br><img style='height:75px;' src='" + get_distributor_logo(distributor) + "'/><br>" + sku;
                        }

                        // When the user clicks on <span> (x), close the modal
                        span.onclick = function() {
                            modal.style.display = "none";
                        }

                    </script>
                </div>
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div class="cockpit-product-search-container">
                        <input class="form-control" type="text" id="cokcpit-product-search-input" placeholder="Enter Search Term (Ex: UPC, SKU, MPN. Manufacturer, Product Name)" onkeypress="if (event.key === 'Enter') loadGrid()"/>
                        <button style="margin-left:5px;" class="btn btn-primary" onclick="loadGrid()">Search</button>
                    </div>
                    <div id="product_feed_table"></div>
                    <div style="padding:5px;margin-top:50px;"><button id="download_inventory_button" class="button alt" data-marker-id="">Download Inventory</button></div>
                    <script>
                        const window_height = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) * 0.8;
                        function get_distributor_logo(code){
                            if (code == "ZND"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_zanders.jpeg";
                            }else if (code == "2AW"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_2aw.png";
                            }else if (code == "CSSI"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_cssi.png";
                            }else if (code == "DAV"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_davidsons.jpeg";
                            }else if (code == "LIP"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_lipseys.jpeg";
                            }else if (code == "RSR"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_rsr.png";
                            }else if (code == "TSW"){
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_sports_south.png";
                            }else{
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_" + code + ".png";
                            }
                            return "";
                        }
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        let productGrid;
                        function loadGrid() {
                            const keyword = document.getElementById('cokcpit-product-search-input').value;
                            if (keyword.length<3){
                                alert("Enter a Search Term with at least 3 characters.");
                                return;
                            }
                            if (productGrid) {
                                productGrid.updateConfig({
                                    server: {
                                        url: `https://ffl-api.garidium.com/product?action=get_filtered_catalog&search=${keyword}`,
                                        method: 'GET',
                                        headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                        },
                                        then: data => JSON.parse(data).products.map(product => [
                                            product.distid, 
                                            product.distsku,
                                            JSON.parse(product.images), 
                                            product.name,
                                            product.upc, 
                                            product.mfg_name,
                                            product.mpn,
                                            product.qty_on_hand, 
                                            product.unit_price,  
                                            product.shipping_cost,
                                            product.total_cost,
                                            product.map_price
                                        ]),
                                        total: data => JSON.parse(data).count
                                    }
                                }).forceRender();
                            } else {
                                productGrid = new gridjs.Grid({
                                    columns: [
                                        {name: 'Dist', width: '60px', formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[0].data)}">`)},
                                        {name: 'SKU'}, 
                                        {sort: false, name: 'Product Image', formatter: (_, row) => gridjs.html(`<a style="cursor:pointer;" onclick="load_product_data('${row.cells[3].data.replaceAll("\"","&quot;") + "','" + row.cells[0].data + "','" + row.cells[1].data + "','" + (row.cells[2].data.length>0?row.cells[2].data[0]['src']:"")}')"><img style="max-height:40px;max-width:100px;height:auto;width:auto;" src="${(row.cells[2].data.length>0?row.cells[2].data[0]['src']:"")}"></a>`)},
                                        {name: 'Name', width: '200px'}, 
                                        {name: "UPC"},
                                        {name: "MFG"},
                                        {name: "MPN"},
                                        {name: "Qty", width: '55px'},
                                        {name: 'Cost', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                        {name: 'Ship', width: '60px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                        {name: 'Total', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                        {name: 'MAP', width: '80px', formatter: (cell) => `${cell!=null?"$" + cell.toFixed(2):""}`}
                                    ],
                                    sort: {
                                        multiColumn: false,
                                        server: {
                                            url: (prev, columns) => {
                                                if (!columns.length) return prev;
                                                const col = columns[0];
                                                const dir = col.direction === 1 ? 'asc' : 'desc';
                                                let colName = ['distid', 'distsku', 'distsku', 'name', 'upc', 'mfg_name', 'mpn', 'qty_on_hand', 'unit_price', 'shipping_cost', 'total_cost', 'map_price'][col.index];
                                                return `${prev}&order_column=${colName}&order_direction=${dir}`;
                                            }
                                        }
                                    },
                                    resizable: true,
                                    pagination: {
                                        limit: 100,
                                        server: {
                                            url: (prev, page, limit) => `${prev}&limit=${limit}&offset=${page * limit}`
                                        }
                                    },
                                    fixedHeader: true,
                                    style: {
                                        td: {
                                            'padding': '3px'
                                        },
                                        th: {
                                            'padding': '3px'
                                        }
                                    },
                                    server: {
                                        url: `https://ffl-api.garidium.com/product?action=get_filtered_catalog&search=${keyword}`,
                                        method: 'GET',
                                        headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                        },
                                        then: data => JSON.parse(data).products.map(product => [
                                            product.distid, 
                                            product.distsku,
                                            JSON.parse(product.images), 
                                            product.name,
                                            product.upc, 
                                            product.mfg_name,
                                            product.mpn,
                                            product.qty_on_hand, 
                                            product.unit_price,  
                                            product.shipping_cost,
                                            product.total_cost,
                                            product.map_price
                                        ]),
                                        total: data => JSON.parse(data).count
                                    } 
                                }).render(document.getElementById('product_feed_table'));
                            }
                        }

                        document.getElementById("download_inventory_button").addEventListener("click", function(){
                            document.getElementById("download_inventory_button").disabled = true;
                            document.getElementById('download_inventory_button').innerText = 'Please Wait...';
                            fetch("https://ffl-api.garidium.com/download", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                },
                                body: JSON.stringify({"action": "download_inventory"})
                            })
                            .then(response=>response.json())
                            .then(data=>{ 
                                window.open(data);  
                                document.getElementById("download_inventory_button").disabled = false; 
                                document.getElementById('download_inventory_button').innerText = 'Download Inventory';     
                            });
                        });
                    </script>
                 

                </div>
            </div>
            <div id="fulfillment" class="tabcontent">
                <div class="postbox" style="padding: 10px;margin-top: 10px;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="order_fulfillment_table"></div>
                    <div style="padding:5px;display: flex;width: 100%;justify-content: space-between;align-items: flex-start;">
                        <div style="flex: 1;">
                            <button id="download_fulfillment_history_button" class="button alt" data-marker-id="">Download Fulfillment History</button>
                        </div>
                        <div id="gunbroker_processor_section" style="display:none;">
                            <table style="width:100%;" align="right">
                                <tr>
                                    <td align="right">
                                        Gunbroker Orders (Beta):
                                        <select id="gunbroker_order_id" class="input-input"></select>
                                        <button id="process_gunbroker_order_button" class="button alt" data-marker-id="">Fulfill</button>
                                        <button id="import_gunbroker_order_button" class="button alt" data-marker-id="">Import</button>
                                    </td>
                                </tr>
                            </table>
                         </div>
                    </div>
                    <script>
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var of_grid = new gridjs.Grid({
                            columns: [
                                {name: 'Source', width: '90px',
                                    formatter: (_, row) => {
                                        if (row.cells[0].data == "gunbroker"){
                                            return gridjs.html(`Gunbroker`);
                                        }else if (row.cells[0].data == "woocommerce"){
                                            return gridjs.html(`Website`);
                                        }
                                    }
                                }, 
                                {name: 'Order', width: '90px',
                                    formatter: (_, row) => {
                                        if (row.cells[0].data == "gunbroker"){
                                            return gridjs.html(`<a target=_blank href="https://www.gunbroker.com/order?orderid=${row.cells[1].data}">${row.cells[1].data}</a>`);
                                        }else{
                                            return gridjs.html(`<a target=_blank href="/wp-admin/post.php?post=${row.cells[1].data}&action=edit">${row.cells[1].data}</a>`);
                                        }
                                    }
                                },
                                {name: 'Dist', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[2].data)}">`)
                                },
                                {name: 'Dist. Order', width: '85px',
                                    formatter: (_, row) => {
                                        if (row.cells[12].data == null){
                                            return gridjs.html(row.cells[3].data + (row.cells[13].data?"&nbsp;<img title=\"Shipped to Store\" width=\"20\" src=\"https://garidium.s3.amazonaws.com/images/store.png\"/>":""));
                                        }else{
                                            return gridjs.html(`<a target=_blank href="${row.cells[12].data}">${row.cells[3].data}</a>` + (row.cells[13].data?"&nbsp;<img title=\"Shipped to Store\" width=\"20\" src=\"https://garidium.s3.amazonaws.com/images/store.png\"/>":""));
                                        }
                                    }
                                },
                                {name: 'Status', width: '100px'}, 
                                {name: 'Customer Name'}, 
                                {name: 'Phone', width: '120px',
                                    formatter: (cell) => `${cell.replace(/\D/g, '').slice(-10).replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')}`
                                },
                                {name: 'Email', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<a href="mailto:${row.cells[7].data}">Email</a>`)
                                },
                                {name: 'Ordered', width: '125px',
                                    formatter: (cell) => `${new Date(new Date(cell.replace(" ", "T") + "Z").getTime() - (new Date(cell.replace(" ", "T") + "Z").getTimezoneOffset() * 60000)).toISOString().split('T')[0]}`
                                },
                                {name: 'Shipped', width: '100px',
                                    formatter: (cell) => `${cell!=null?new Date(new Date(cell.replace(" ", "T") + "Z").getTime() - (new Date(cell.replace(" ", "T") + "Z").getTimezoneOffset() * 60000)).toISOString().split('T')[0]:""}`
                                },
                                {name: "Ship Status",
                                    formatter: (_, row) => {
                                        const hideIcon = `<a style="margin-right:3px;cursor:pointer; float:right;" onclick="hideOrder('${row.cells[2].data}', '${row.cells[1].data}', '${row.cells[3].data}')">
                                            <i class="fas fa-eye-slash" style="color:gray;"></i></a>`;
                                        if (row.cells[11].data == "delivered"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:green;">Delivered</span></a>`);
                                        }else if (row.cells[11].data == "return_to_sender"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:red;">Return to Sender</span></a>`);
                                        }else{
                                            if (row.cells[10].data != null) {
                                                return gridjs.html(`<a target=_blank href="${row.cells[10].data}">${row.cells[11].data==null?"In Transit":row.cells[11].data}</a>`);
                                            }else{
                                                return gridjs.html((row.cells[11].data!=null?row.cells[11].data:"") + `${hideIcon}`);
                                            }
                                        }
                                    }
                                },
                                {name: "Order URL", hidden: true},
                                {name: "Tracking URL", hidden: true},
                                {name: "Ship-to-Store", hidden: true},
                                
                            ],
                            sort: {
                                multiColumn: false,
                                server: {
                                    url: (prev, columns) => {
                                        if (!columns.length) return prev;
                                        const col = columns[0];
                                        const dir = col.direction === 1 ? 'asc' : 'desc';
                                        let colName = ["order_source","order_id", "distid", "distributor_order_id", "order_status", "customer_name", "customer_phone", "customer_email", "order_date", "ship_date", "ship_status"][col.index];
                                        let sortUrl = `${prev}&order_column=${colName}&order_direction=${dir}`;
                                        return sortUrl;
                                    }
                                }
                            },
                            search: {
                                server: {
                                    url: (prev, keyword) => `${prev}&search=${keyword}`
                                }
                            },
                            resizable: true,
                            pagination: {
                                limit: 100,
                                server: {
                                    url: (prev, page, limit) => `${prev}&limit=${limit}&offset=${page * limit}`
                                }
                            },
                            fixedHeader: true,
                            style: {
                                td: {
                                    'padding': '3px'
                                },
                                th: {
                                    'padding': '3px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com/product?action=get_order_history',
                                method: 'GET',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
			                    },                                
                                then: data => JSON.parse(data).orders.map(order => [
                                                                   order.order_source,
                                                                   order.order_id, 
                                                                   order.distid, 
                                                                   order.distributor_order_id,
                                                                   order.order_status,
                                                                   order.customer_name,
                                                                   order.customer_phone,
                                                                   order.customer_email,
                                                                   order.order_date,
                                                                   order.ship_date, 
                                                                   order.ship_tracking_url,
                                                                   order.ship_status,
                                                                   order.order_url,
                                                                   order.ship_to_store
                                                                ]),  
                                                                 
                                total: data => JSON.parse(data).count
                            } 
                        });
                  
                        function hideOrder(distid, order_id, distributor_order_id){
                            if (window.confirm("Are you sure you want to hide this distributor order, disassociating it from this Order? It will be erased from your view (but still in the database). It will be ignored from Cockpit from now until the end of time. This action WILL NOT CANCEL the order with the distributor.")){
                                if (window.confirm("Are you REALLY SURE?? We will not take requests to unhide an order because you did it by mistake..., and again.. this WILl NOT CANCEL the order with your distributor.")){
                                    try{
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                            },
                                            body: JSON.stringify({"action": "hide_distributor_order", "data": {"distid": distid,"order_id": order_id, "distributor_order_id":distributor_order_id, "api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{  
                                            if (!data.success){
                                                alert("Failed to Hide the Order, contact support@garidium.com so we can address the problem ASAP.");
                                            }  
                                            of_grid.forceRender();
                                        });
                                    } catch (error) {
                                        console.error(error);
                                    }
                                }
                            }
                        }
                        
                        document.getElementById("download_fulfillment_history_button").addEventListener("click", function(){
                            document.getElementById("download_fulfillment_history_button").disabled = true;
                            document.getElementById('download_fulfillment_history_button').innerText = 'Please Wait...';
                            fetch("https://ffl-api.garidium.com/download", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                },
                                body: JSON.stringify({"action": "download_fulfillment_history"})
                            })
                            .then(response=>response.json())
                            .then(data=>{ 
                                window.open(data);  
                                document.getElementById("download_fulfillment_history_button").disabled = false; 
                                document.getElementById('download_fulfillment_history_button').innerText = 'Download Fulfillment History';     
                            });
                        });
                        
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                            },
                            body: JSON.stringify({"action": "list_gunbroker_orders", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                        })
                        .then(response=>response.json())
                        .then(data=>{ 
                            // Get the dropdown element
                            const dropdown = document.getElementById('gunbroker_order_id');
                            if (data.order_data && data.order_data.length > 0){
                                // Loop through the data and create an option for each item
                                data.order_data.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.orderID;
                                    timestamp = new Date(item.orderDateUTC).toLocaleString('en-US', {timeZone: 'UTC'});
                                    option.text = item.orderID + " - " + timestamp + " - " + item.billToName;
                                    dropdown.appendChild(option);
                                });

                                document.getElementById("gunbroker_processor_section").style.display="";
                                document.getElementById("process_gunbroker_order_button").addEventListener("click", function(){
                                    if (window.confirm("This will place a new distributor order, fulfilling GunBroker Order#" + document.getElementById("gunbroker_order_id").value + ". Are you sure you need a new order placed, and do you want to proceed?")){
                                        document.getElementById("process_gunbroker_order_button").disabled = true;
                                        document.getElementById('process_gunbroker_order_button').innerText = 'Please Wait...';
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                            },
                                            body: JSON.stringify({"action": "place_distributor_order", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>" , "order_source": "gunbroker", "order_id": document.getElementById("gunbroker_order_id").value}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{ 
                                            document.getElementById("process_gunbroker_order_button").disabled = false; 
                                            document.getElementById('process_gunbroker_order_button').innerText = 'Fulfill';     
                                            if (data.Error != null && data.Error != undefined){
                                                alert(data.Error);
                                            }else{
                                                of_grid.forceRender();
                                            }
                                        });
                                    }
                                });

                                document.getElementById("import_gunbroker_order_button").addEventListener("click", function(){
                                    if (window.confirm("This will import GunBroker Order#" + document.getElementById("gunbroker_order_id").value + ", creating an equivalent WooCommerce Order. The order will come over with the email and selected FFL in the order comments section, so that the customer isn't emailed. This is a beta feature in testing, Are you sure you want to proceed?")){
                                        document.getElementById("import_gunbroker_order_button").disabled = true;
                                        document.getElementById('import_gunbroker_order_button').innerText = 'Please Wait...';
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                            },
                                            body: JSON.stringify({"action": "import_gunbroker_order", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>" , "gunbroker_order_id": document.getElementById("gunbroker_order_id").value}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{ 
                                            document.getElementById("import_gunbroker_order_button").disabled = false; 
                                            document.getElementById('import_gunbroker_order_button').innerText = 'Import';     
                                            if (data.Error != null && data.Error != undefined){
                                                alert(data.Error);
                                            }else{
                                                alert("Gunbroker Order Imported, go to the WooCommerce order page to view Order.");
                                            }
                                        });
                                    }
                                });
                            }
                        });
                        
                    </script>
                </div>
            </div>         
            <div id="logs" class="tabcontent">
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <a class="button alt" onclick="log_grid.forceRender();">Refresh</a>                 
                    <div id="log_table"></div>
                    <a class="button alt" onclick="log_grid.forceRender();">Refresh</a>                 
                    <script>
                        const window_height2 = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) * 0.8;
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var log_grid = new gridjs.Grid({
                            columns: [
                                {name: 'Timestamp', width: '185px', formatter: (cell) => `${new Date(cell).toLocaleString()}`}, 
                                {name: "Message"}
                            ],
                            resizable: true,
                            fixedHeader: true,
                            style: {
                                td: {
                                    'padding': '3px',
                                    'background': 'black',
                                    'color': '#a1e89f',
                                    'border': 'solid #353333 1px'
                                },
                                th: {
                                    'padding': '3px',
                                    'background': 'black',
                                    'color': '#a1e89f',
                                    'border': 'solid #353333 1px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com',
                                method: 'POST',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>",
			                    },
                                body: JSON.stringify({"action": "get_logs", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey);?>", "log_count": 2}}),
                                then: data => JSON.parse(data).logs.map(log => [
                                                                   log.timestamp, 
                                                                   log.message])
                            } 
                        })
                    </script>
                </div>
            </div>
            <div id="help_center" class="tabcontent">
                <script>
                    function load_help_videos(){
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                            },
                            body: JSON.stringify({"action": "get_training_videos"})
                        })
                        .then(response=>response.json())
                        .then(data=>{ 
                            var video_div = document.getElementById("training_videos");
                            video_div.innerHTML = '';
                            for (var i = 0, l = data.length; i < l; i++) {
                                var innerDiv = document.createElement("div");
                                innerDiv.innerHTML = '<div style="width:390px;"><iframe width="370" height="208" src="' + data[i].url + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe><br><span style="width:99%;">' + data[i].title + '</span><br><span style="color:#a29f9f !important;width:99%;height:50px;">' + data[i].description + '</span></div>';
                                video_div.appendChild(innerDiv);
                            }
                        });
                    }
                </script>
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <div style="padding:10px;">If you have any questions about FFL Cockpit configuration, send an email to support@garidium.com detailing your questions. We'll get back as soon as we can. Include your name and your website URL so we can look up your configuration. Also, make to review our <a target=_blank href="https://garidium.com/category/help-center/">Help Center</a> for tips on using Cockpit.</div>
                    <div class="video_grid" id="training_videos"></div>
                </div>
            </div>    
            <table style="width:100%;">
                <tr>
                    <td style="width:150px;font-weight:bold;" scope="row">FFL Cockpit Key:</td>
                    <td>
                    <form method="post" action="options.php" style="display: flex; align-items: center;">
                        <?php settings_fields('g-ffl-cockpit-settings'); ?>
                        <input oninput="document.getElementById('set_key_form').style.display='inline';" type="password" style="width: 350px;" name="g_ffl_cockpit_key" id="g_ffl_cockpit_key" 
                            aria-describedby="login_error" class="input password-input" size="20"
                            value="<?php echo esc_attr($gFFLCockpitKey); ?>"/>
                        <span id="set_key_form" style="display: none; margin-left: 10px;">
                            <?php submit_button('Set Key', 'primary', 'submit-button'); ?>
                        </span>
                    </form>
                    </td>
                    <td align="right">
                        <div id="g-ffl-admin-buttons" align="right" style="margin:20px;display:none;">
                            <b>Admin Functions:&nbsp;</b>
                            <a class="button alt" onclick="document.getElementById('configuration').style.border='solid red 3px';initialCockpitConfiguration = null;get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value, true);document.getElementById('admin_current_editing_key').innerHTML = 'Editing: ' + document.getElementById('g_ffl_cockpit_key').value;document.getElementById('admin_current_editing_key').style.display='';document.getElementById('save_cockpit_configuration_button').style.display='none';">Load Config</a>
                            <a class="button alt" onclick="setConfig(document.getElementById('g_ffl_cockpit_key').value)?refreshEditor():alert('Save Failed');">Save</a>
                            <span style="padding:10px;color:red;display:none;" id="admin_current_editing_key"></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    <?php }
}

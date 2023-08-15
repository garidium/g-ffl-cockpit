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
        add_menu_page('g-FFL Cockpit Settings Page', $custom_plugin_name, 'manage_options', 'g-ffl-cockpit-settings', array($this, 'g_ffl_cockpit_settings_page'), 'dashicons-location-alt', 70);
        add_action('admin_init', array($this, 'register_g_ffl_cockpit_settings'));
    }

    function register_g_ffl_cockpit_settings()
    {
        //register our settings
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_key');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_configuration');
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

        <div class="wrap">
            <img src="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>">
            <br><br>
            <!-- Tab links -->
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'configuration')" id="defaultOpen">Configuration</button>
                <button class="tablinks" onclick="openTab(event, 'product_feed');product_grid.render(document.getElementById('product_feed_table'));">Product Feed</button>
                <button class="tablinks" onclick="openTab(event, 'fulfillment');of_grid.render(document.getElementById('order_fulfillment_table'));">Fulfillment</button>
                <button class="tablinks" onclick="openTab(event, 'logs');log_grid.render(document.getElementById('log_table'));">Logs</button>
                <button class="tablinks" onclick="openTab(event, 'help_center');load_help_videos();">Help Center</button>
            </div>
            <!-- Tab content -->
            <div id="configuration" class="tabcontent">
            <!--<h3>Configuration</h3>-->
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                    <form method="post" action="options.php" onSubmit="return setConfig('<?php echo esc_attr($gFFLCockpitKey);?>');">
                        <?php settings_fields('g-ffl-cockpit-settings'); ?>
                        <table class="form-table">
                        <tr>
                                <td style="vertical-align:top;width:150px;font-weight:bold;" scope="row">g-FFL Cockpit Key:</td>
                                <td style="padding:5px;vertical-align:top;">
                                    <div class="user-pass-wrap">
                                        <div class="wp-pwd">
                                            <input type="password" style="width: 350px;" name="g_ffl_cockpit_key" id="g_ffl_cockpit_key" 
                                                aria-describedby="login_error" class="input password-input" size="20"
                                                value="<?php echo esc_attr($gFFLCockpitKey); ?>"/>
                                                <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value, false);">Load Config</a>
                                        </div>
                                        <p>Email sales@garidium.com to get a key, or if your key has expired.</p>
                                    </div>
                                </td>
                                <td>
                                    <div id="g-ffl-admin-buttons" align="right" style="margin:5px;display:none;">
                                        <b>Admin Functions:&nbsp;</b>
                                        <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value, true);document.getElementById('admin_current_editing_key').innerHTML = 'Editing: ' + document.getElementById('g_ffl_cockpit_key').value;document.getElementById('admin_current_editing_key').style.display='';document.getElementById('submit_button_div').style.display='none';">Load Config</a>
                                        <a class="button alt" onclick="setConfig(document.getElementById('g_ffl_cockpit_key').value);">Save</a>
                                        <br><br><span style="padding:10px;color:red;display:none;" id="admin_current_editing_key"></span>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top">
                                <td colspan=3>
                                <div id="jsoneditor" style="width: 100%; height: 500px;"></div>
                                <input type="hidden" name="g_ffl_cockpit_configuration" id="g_ffl_cockpit_configuration">
                                <script>
                                    function get_and_set_cockpit_configuration(api_key, isAdminRequest){
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
                                                    cockpit_configuration = JSON.parse(data[0].cockpit_configuration);
                                                    editor.set(cockpit_configuration);
                                                } catch (error) {
                                                    alert("No configuration found for this key, setting to default.");
                                                }
                                            });
                                        }else{
                                            alert("No API Key Configured");
                                        }
                                    }
                                </script>
                                <script>
                                    var all_brands = ["10 Ring Marketing / WRJ", "1791 GUNLEATHER", "2 Monkey Trading", "2A Armament", "2nd Amendment 1791 LLC", "30-06 Outdoors", "3M Peltor", "3M/Peltor", "A-Zoom", "Absolute Outdoor Inc", "Accu-Tac", "ACCUFIRE TECHNOLOGY INC", "Accura Bullets", "Accurate", "Accurate Powders", "Accusharp", "ACE", "Action Target", "ACTION TARGET INC", "Adams Arms", "ADAPTIVE TACTICAL", "ADCO", "ADCo International", "Advance Warrior Solutions", "Advance Warrior Solutions LLC", "Advanced Armament Corp", "Advanced Tactical Firearms Int'l Corp.", "Advanced Tech", "Advanced Technology", "Advanced Technology Inc.", "Advantage Arms", "AdvenTec LLC dba Rig Em Right Outdoors", "Adventure Medical Kits", "Aero Precision", "AeroPrecision", "After Bite", "AGM Global Vision", "Aguila", "Aguila Ammunition", "Aim Sports", "AimCam", "AimShot", "Aimtech", "Akkar", "Alex Pro Firearms", "Alexander Arms", "ALG Defense", "Alien gear", "Alien Gear Holsters", "Allen", "ALLIANCE CONSUMER GROUP", "Alliant", "Alliant Powder", "Alpha Munitions", "ALPS Brands", "Altus Brands", "Altus Brands Llc - Pro-Ears", "AMAC (American Militray Arms Corp)", "Amend2", "American Buffalo Knife & Tool", "American Built Arms Company", "American Classic", "American Cowboy Ammo", "American Defense", "American Defense Mfg", "American Hunter", "AMERICAN PRECISION ARMS", "American Tactical / ATI", "American Technologies Network", "Ameriglo", "Ameristep/Evolved Ingenuity", "Ammo Inc", "Ammo Inc.", "AMMO INCORPORATED", "Ammunition Storage Components", "AMS Bowfishing", "Anderson Manufacturing", "Andro Corp Industries", "Angstadt Arms", "Ani-Logics Outdoors", "Anschutz", "Antler King", "AOB", "APEX TACTICAL SPECIALTIES", "Apex Tactical Specialties Inc.", "Aquamira", "Ar-mor", "ARB", "Archangel", "Arctic Ice", "Arctic Shield", "Arex", "Arisaka Defense", "ARMA ZEKA", "Armalite", "Armasight", "ARMASIGHT (THIRD BULL)", "Armaspec", "Armsco", "Armscor/Rock Island Armory", "Arsenal Firearms", "Arsenal, Inc.", "Asc", "ATA Arms", "ATA SHOTGUNS/FOXTROT LLC", "Athlon", "Athlon Optics", "ATI Outdoors", "Atl Arms/ Specialty Cartridge Inc DBA ACME", "ATLANCO/ATLANTA ARMY NAVY", "ATN", "Atomic", "Atomic Ammunition", "Atsko", "Audeo Sum Inc DBA FrogLube", "Auto Ordnance", "Auto-Ordnance", "AVIAN", "Avian X", "AVIAN X (GSM)", "Axcel", "Axe", "Axe Crossbows", "Axeon", "AXON/TASER (LC PRODUCTS)", "B-Square", "B&T FIREARMS", "B&T Weapons", "B3 Archery", "B5 Systems", "Backup Tactical", "Ballistic Adv", "Ballistic Advantage", "Ballistol", "BAREBONE OUTDOORS FLASHLT", "Barnaul Ammunition", "Barnes", "Barnes Bullets", "Barrett Firearms", "Barska", "Baschiere & Pellagri USA Inc.", "Bastion", "Battenfeld", "Battenfeld Knives", "Battle Arms Development", "Bayco", "Bayco Products dba NightStick", "Bear & Son", "Bear Archery", "Bear Edge", "Bear Ops", "Beartooth Products", "BECK TEK, LLC (TEKMAT)", "Bee Stinger", "Beeman", "Benchmaster", "Benjamin", "Benjamin Sheridan", "Beretta", "Beretta Air Pistol", "Beretta USA", "Bergara", "Berger Bullets", "Berry's Mfg", "Berrys", "Bersa", "Bianchi", "Big Bobber", "Big Shot Archery", "Big Sky Racks", "Bino Dock", "Birchwood Casey", "Black Aces Tactical", "BLACK HILLS AMMUNITION", "Black Rain", "Black Rain Ordnance", "Black Rain Ordnance Inc", "Black Spider LLC", "BLACKHAWK", "Blackhorn", "BlackPoint", "BlackPoint Tactical", "Blackpowder Products Inc-Quake", "Blaser USA", "Blazer Ammunition", "Block Targets", "Blue Book", "Blue Book Publications", "Blue Force Gear", "Blue Line Diana", "BLUE LINE GLOBAL", "Bob Allen", "Bog", "Bog-Pod", "Bohning", "Bond Arms", "BoneView", "Bootleg", "Bore Tech", "BoreSnake", "Bounty Hunter", "Bowden Tactical", "Boyt Harness", "Boyt Harness Company", "Brass Guys Llc", "Bravo", "Bravo Company Mfg / BCM", "Bravo Concealment", "Break Free", "Break-Free", "BreakFree", "Breakthrough Clean", "Breakthrough Clean Technologies", "Breakthrough cleaning", "Breda/Dickinson", "Brenneke", "Brigade Firearms", "Brigade Mfg.", "BRNO", "Browning", "Browning Ammo", "Browning Clothing", "Browning Trail Cameras", "BSA", "BSA Optics", "Bubba Blade", "BUBBA BLADE/BATTENFIELD", "Bubba Rope", "Buck Bomb", "Buck Wear", "Buffalo Bore Ammunition", "Buffalo Cartridge Company", "BUL ARMORY USA", "Bull Frog", "Bulldog", "Bulldog Cases", "BulletSafe", "Burris", "Burris Company Inc.", "Bushmaster", "Bushnell", "Butchs", "Butler Creek", "BUY GUNS LLC/BIRD DOG ARM", "BYRNA TECHNOLOGIES", "Byrna Technologies Inc.", "C Products Defense", "C Products Defense Inc.", "C-More Systems", "C&E Innovations DBA Amend2", "C&G HOLSTERS", "C&H Precision Weapons", "CAA", "Cajun Bowfishing", "Caldwell", "Camelbak", "Cameleon", "Camo Unlimited", "Camoclad Inc.", "CANIK", "CANYON ARMS,LLC", "Capsule Feeders", "Carbon Express", "Carlson", "Carlson'S Choke Tubes LLC", "Carlsons", "Case", "Cass Creek", "Cci", "CenterPoint", "Century Arms", "Cerus Gear", "CGS SUPPRESSORS", "Champion", "Champion Targets", "Champion Traps & Targets", "Chaos Gear Supply", "Charles Daly", "Charles Daly Chiappa", "Charter Arms", "CHEYTAC (CAMPBELL ARMS)", "Cheytac USA", "Chiappa Firearms", "CHILD GUARD", "Chip McCormick Custom", "Chip Mccormick Custom Llc", "Chipmunk", "Christensen Arms", "Cimarron Firearms", "Citadel", "CIVIVI", "CLEAN CONTROL/LETHAL PROD", "Cleanshot", "Clenzoil", "Cloud Defensive", "CLOUD DEFENSIVE LLC", "CMC Products", "CMC Triggers", "CMMG", "Cobra Archery", "COBRA PISTOL/BEARMAN IND", "COBRA TEC KNIVES LLC", "Cobratec Knives", "Code Blue", "Codeblue Div Of Ebsco", "Cold Steel", "Cole-TAC", "Coleman", "Colt", "Colt Air Pistol", "Colt Mfg", "Colt Rimfire", "Columbia River", "Columbia River Knife & Tool", "Command Arms", "Command Arms Accessories Tac.", "Comp-Tac", "Competition Electronics Inc.", "Concept Development Corp.", "Connecticut Valley Arms / CVA", "ConQuest", "Cor-Bon", "CorBon", "Corbon Inc.", "CORROSION TECHNOLOGIES", "Counter Assault", "Covert Camera's", "Covert Cameras", "Covert Scouting Cameras", "CRANFORD MFG.", "Crimson Trace", "Crimson Trace Corporation", "CRKT Knives", "Crosman", "CROSMAN CORP", "Cross Armory", "Cross Industries", "Crossfire", "Crow Shooting Supply", "Crucial Concealment", "Cruxord", "Cuddeback", "Custom Bow Equipment", "CVA MASS MARKET", "Cyclops", "CZ-USA", "D-Lead", "D.T. Systems", "D&H Tactical", "DAC", "Dac Technologies", "Daisy", "Daisy Manufacturing Co. Inc.", "Dan Wesson", "Daniel Defense", "Davey Crickett", "DDUPLEKS USA INC", "Dead Air", "Dead Air Armament", "Dead Air Silencers", "Dead Down Wind", "DEAD DOWN WIND (ARCUS)", "Dead Down Wind DBA Arcus Hunting", "Dead Foot", "Del-Ton", "Del-Ton Inc", "DeSantis", "DeSantis Gunhide", "Desantis Leather Goods Co.", "Desert Tech", "Diamondback", "Diamondback Barrels", "Diamondback Firearms", "DIAMONDBACK KNIFEWORKS", "Diamondhead Usa Inc.", "DNZ", "DNZ Products", "Dnz Products Llc", "Do All OutdoorsLlc", "Do All Traps", "Double Take Archery", "DoubleStar", "DoubleTap Ammunition", "DPMS", "Drago Gear", "DRD Tactical", "Drymate", "Duck Commander", "Duell Shot Outdoors LLC", "Dupont - IMR Powder", "Dura Sight", "DURAMAG", "E.M.F. Company Inc", "EAA", "Eagle Industries", "EAR", "Easton", "Ed Brown", "Edgar Sherman Design", "Edge Eyewear", "El Paso Saddlery", "Element Outdoors", "Eley", "Elftmann Tactical", "Eliminator Spray", "Elite Tactical Systems Group", "Energizer", "Engineered Materials Inc. DBA Tetra Gun", "Enlight Group dba Jagemann Munition Components", "Environ Metal Inc", "EOTech", "Ergo", "Ergo Grip", "Ergo Grips / Falcon Ind. Inc.", "ESCA Tech", "Escort", "Estate", "Estate Cartridge Co", "ET Arms", "ET ARMS INC", "ETS Group", "European American Armory / EAA Corp", "Eurosports LLC", "Eva-Dry", "Evolution Gun Works", "Evolution Outdoor", "Excel", "Excel Arms", "Exothermic Technologies", "EZR Sport", "F.A.B. Defense", "FAB DEFENSE (USIQ)", "FAB DEFENSE INC.", "Fail Zero", "FailZero", "FAUSTI USA, INC", "Faxon Firearms", "Fechheimer", "Federal", "Federal Cartridge Co.", "FENIX WHOLESALE", "FERADYNE INC", "Feradyne Outdoors", "FIERCE FIREARMS", "FightLite Industries", "FIME", "Fime Group", "Fiocchi", "Fiocchi Ammunition", "Firearm Safety Devices", "Firearm Safety Devices Corporation", "FIREBIRD USA", "Firefield", "First Samco/Fobus", "Flambeau", "Flambeau Outdoors", "Flextone Calls", "Flextone Decoys", "FLIR", "Flitz", "Flitz International", "Flitz International Ltd", "Flying Arrow Archery", "FMK", "FMK Firearms", "FN America / FN Herstal", "Fobus", "Forster Products", "FORSTER PRODUCTS INC", "FORT SCOTT MUNITIONS", "Fortis Manufacturing, Inc.", "Fortune Products (Accusharp)", "FosTech Outdoors", "Fox Labs", "Foxpro", "Frankford Arsenal", "Franklin Armory", "Freedom Ordance", "Freedom Ordnance", "Frogg Toggs", "FrogLube", "FRONTIER", "Frontier Cartridge", "Frost Cutlery", "FSDC", "Full Forge Gear", "Fusion Firearms", "FUSION PRECISION", "G-Outdoors Inc.", "G-SHOCK/VLC DISTRIBUTION", "G*Outdoors", "G2 Research", "G2 Research Inc.", "G5 OUTDOORS", "G96", "G96 Products", "Galco", "Galco International Ltd.", "Gamo", "Gamo Usa Corp.", "Garaysar", "Garmin", "Gatco", "Gator Waders", "Geissele Automatics", "Gemtech", "GERMAN PRECISION OPTICS", "German Sport Guns / GSG", "GForce Arms", "GG TELECOM /SPYPOINT", "GG&G, Inc.", "Ghost Inc", "Ghost Inc.", "Gilboa/Silver Shadow", "Gilmore Sports Concepts", "Girls With Guns", "Girsan Firearms", "Glaser", "Glaser Ammunition", "GlenDel", "GLOBAL DEFENSE TRADE", "Glock", "Glock Air Pistol", "God'a Grip", "Gold Tip", "Golden Rod", "Gorilla", "Gorilla Ammunition Company LLC", "GPS", "Grace USA", "Grasso Holdings Inc. / Tulammo", "GREAT LAKES FIREARMS", "Great Lakes Firearms & Ammo", "Great Lakes Firearms & Ammunition", "Green Mountain", "Grey Ghost Gear", "Grey Ghost Precision", "Griffin Armament", "Grim Reaper", "Grip Pod", "GrovTec", "Grovtec US Inc", "Grovtec Us Inc.", "GSG German Sports Guns", "Gsm", "Guard Dog", "Guard dog security", "Gun Guard", "Gun Storage Solutions", "GunMate", "Gunslick", "Guntec USA", "GunVault", "GunVault Inc.", "GUNWERKS LLC", "H&N Sport Pellets", "Haas Outdoors", "HABIT", "HALO (GSM)", "Halo Optics", "Hammerli", "Hamskea Archery", "Hardigg Storm Case", "Harris", "Harris Engineering", "Harris Engineering Inc.", "Harvester", "Hastings", "Hatfield", "Hatsan Airguns", "Hatsan USA / Escort Shotguns", "Havalon", "Havalon Knives", "Hawk", "Haydel's", "Haydel's Game Calls", "Haydels", "Heckler & Koch / H&K", "Heizer Defense", "HELIX 6 (USIQ)", "Henry Repeating Arms", "Hera", "Hera USA", "Heritage Arms", "Heritage Mfg", "HEVI-METAL (VISTA)", "HEVI-Shot", "Hevishot", "HEXMAG", "HHA Sports", "Hi Point Firearms", "Hi-Viz", "HIGDON DECOYS", "Higdon Outdoors", "High Mountain Defense Inc dba - Old West Firearms", "HIGH SPEED GEAR", "Hiperfire", "Hiviz", "HKS", "Hks Speed Loaders", "HME", "HME Products", "Hodgdon", "Hodgdon Powder Company Inc.", "Hogue", "Hogue Inc.", "Holosun", "Holosun Technologies", "Homeland", "Honeywell Safety Products", "Honeywell Safety Products USA", "Hook's Custom Calls", "Hooyman", "Hoppe's", "Hoppes", "HORIZON DESIGN", "Hornady", "Hornady Mfg", "Hornady Reloading", "Hot Shot", "Hot Shot Archery", "HotHands", "Howa", "Howard Leight", "HSM", "HSM Ammo", "Humvee Accessories", "Huntego Limited DBA Cleanshot", "Hunter Company", "Hunter Company Inc", "Hunter Safety System", "Hunter's Kloak", "Hunters Specialties", "Hunters Specialties Inc.", "Huntime Inc", "Huntwise dba MOJO Outdoors", "Hurricane", "Huskemaw Optics", "HUXWRX Safety Company", "HYPERION MUNITIONS", "iHunt", "Impact Weapons Components", "Impala Plus", "IMR", "Industrial Revolution", "INFORCE", "Inland Manufacturing", "Inland Mfg", "Insights Hunting", "International Firearm Corporation", "Iosso Products", "IOTA (HORIZON FIREARMS)", "IQ Bowsight", "IRAY USA LLC", "IRAYUSA", "IRON CITY RIFLE WORKS", "Istanbul Silah", "Italian Firearms Group", "Iver Johnson", "IWI - Israel Weapon Industries", "IWI US", "IWI US (Israel Weapon Industries)", "J-Ron Inc.", "J.Dewey Mfg.Co.Inc.", "J.P. Sauer & Sohn", "J&E Machine Tech", "Jackson Safety", "Jacob Grey", "Jagemann Precision Plastics", "JEBS Choke Tubes Inc", "JERENT ENTERPRISES SONIC BOOM", "Johnny Stewart", "Johnson Outdoors Inc DBA Eureka Tent Jetboil", "JTS Shotgun (XISICO USA)", "Juggernaut Tactical", "Jurassic Rock", "Just Right Carbine", "Just Right Carbines", "Ka-Bar", "Ka-Bar Knives", "Ka-Bar Knives Inc.", "KABAR", "Kahr Arms", "Kalashnikov USA", "Kano Laboratories Inc.", "KCI USA", "KCI USA Inc", "KCI Usa Inc.", "KE Arms", "Kel-tec", "KelTec", "Keng's", "Keng's Firearms Specialty Inc", "Kent Cartridge", "Kent Cartridge America", "Kershaw", "Kershaw Knives", "Kestrel", "Kestrel Ballistics", "KESTREL METERS", "Kick Eez Products", "Kick's Industries", "Kimber", "Kinetic Development Group, LLC", "KINGPORT INDUSTRIES LLC", "Kleen Bore", "Kleen-Bore", "Knight & Hale Game Calls", "Knight Rifles", "Knights Armament Company", "KNIGHTS MFG COMPANY", "KNS Precision", "KNS Precision, Inc.", "KOBAYASHI CONSUMER PROD", "Kolpin", "Konus", "Konus Optics", "Koola Buck", "KOPFJAGER/SELLMARK", "Kriss TDI", "Kynshot", "L-3 Communications- Eotech", "L.A.G. Tactical Inc.", "L.A.G. Tactical, Inc.", "Lacrosse Footwear Inc.", "LAG TACTICAL INC", "Lancer", "Landor Arms", "LANGDON TACTICAL TECH", "Lansky Sharpeners", "Lantac USA", "Lapua", "Laser Max Inc.", "Laserlyte", "LaserMax", "LBE Unlimited", "Leapers, Inc. - UTG", "Leather Brothers Inc. DBA Omnipet", "Lee", "Lee Armory", "Lee Precision", "Legacy Sports International", "Lehigh Defense LLC", "LEM Products", "Leupold", "Leupold & Stevens Inc.", "Liberty", "Liberty Ammunition", "Lightfield", "Lightning Ammo", "LIMBSAVER", "Live Free Armory", "LKCI", "Lockdown", "Lone Wolf Distributors", "LONGSHOT TARGET CAMERA", "Lucas Oil", "Lucid Optics", "Lumenok", "Luth-AR", "LUXUS ARMS (HM DEFENSE)", "LWRC", "Lyman", "M-PRO 7", "M-Pro7", "M&P Accessories", "M+M", "M+M Inc", "Mace", "Mace Personal Defense", "Mace Security International", "Mag Storage Solutions", "Mag-Tech/Sellier- Bellot", "maglula", "Maglula ltd.", "MagnetoSpeed", "Magnum Research", "Magnus Broadheads", "MagPod", "Magpul", "Magpul Accessories", "Magpul Industries", "MAGPUL INDUSTRIES CORP", "Magpump", "Magtech", "Magtech Ammunition CompanyInc", "Manticore Arms", "Manticore Arms, Inc.", "Mantis", "Mark 7", "Marksman", "Marlin", "Marolina DBA HUK", "Master Cutlery", "Master Lock", "MasterLock", "MasterPiece Arms", "Matrix Diversified Ind", "Mauser", "Maverick Arms", "Max Ops", "Max-Ops", "Maxim", "Maxim Defense Industries", "Maxpedition", "Mayville Engineering Co./Mec", "McCOY", "McCoy Shotguns", "MDT", "MDT SPORTING GOODS INC", "MEC", "MEC-GAR", "Mec-Gar Magazines", "Mec-Gar Usa Inc", "Mecgar", "Mechanix Wear", "Mechanix Wear LLC", "MEPRO USA LLC", "Meprolight", "Meprolight Sights", "Mesa Tactical", "META TACTICAL LLC", "Metalform Magazines", "Michaels", "Michaels Of Oregon", "MID-EVIL INDUSTRIES", "Midwest Industries", "MIDWEST INDUSTRIES INC", "MIGRA AMMUNITION", "Migra Ammunitions LLC", "Military Arms Corporation", "Military Products", "Millennium", "Millennium Marine", "Millennium Treestands", "Millett", "Mirtek Inc.", "Miscellaneous Accessory Items", "Mission Archery", "Mission First Tactical", "MO TACTICAL PRODUCTS LLC", "Mobile Warming", "Modern Muzzleloader", "Moisture King", "MoJack Distributors DBA Scent Crusher", "Mojo", "MOJO OUTDOORS", "Montana Decoy Co.", "Montana Decoy Company", "Montana X-Treme", "Morakniv", "Morrell Targets", "Mossberg", "Mossy Oak Apparel Company/Sale", "Moultrie", "Moultrie Enterprises", "Mountain Mike's", "Mountain Tactical", "Mr.Heater", "MTM", "Mtm Molded Products Company", "Muddy", "Muzzy", "NAA", "NANUK (PLASTICASE INC)", "NAP", "Natrapel", "NCSTAR", "NcSTAR Inc.", "Nemo Arms", "New Archery Products", "Nexbelt", "Night Fision", "Night Fision LLC", "Nightstick", "Nine Line Apparel", "Nite Lite Company", "Nobelsport", "Nockturnal", "Nomad", "Non Typical Inc./Cuddeback", "Nordic Components", "NORMA AMMUNITION (RUAG)", "North American Arms", "North American Rescue", "North Pass Ltd. Hiviz", "Northeast Products", "Nose Jammer", "Nosler", "Nosler Bullets", "Nosler Bullets Inc.", "Noveske Rifleworks", "NovX", "NovX Ammunition", "NXT Generation Toys", "OAKLEY (LUXOTTICA)", "OAKS WHOLESALE DIST/IVER", "Odin Works", "Old Timer", "Old West", "On Time", "On Time Wildlife Feeders", "OPSOL", "OPSol Texas", "Optical Dynamics", "OPTIMAX TECHNOLOGY LLC", "Orthos", "Otis", "Otis Products Inc.", "Otis Technology", "Otter Creek Labs", "Otto", "Outdoor Cap", "Outdoor Cap Company", "Outdoor Connection", "Outdoor Edge", "OUTDOOR PRODUCT INNOVATIO", "Outers", "Pachmayr", "Pachmayr Ltd Tacstar", "Pachmayr/Tacstar Division", "Para Ordnance", "Pathfinder", "Patriot Ordnance Factory", "Peace Keeper", "Pearce Grip", "Pearce GripInc.", "Pearce Grips Inc.", "Peet Dryer", "Pelican", "PepperBall", "Peregrine Outdoors", "Personal Security Products", "Phase 5", "PHASE 5 WEAPON SYSTEMS", "PHOENIX ARMS", "Phoenix Technologies", "Phone Skope", "Pietta", "PIETTA (EMF COMPANY INC)", "Pine Ridge Archery", "PINEY MOUNTAIN AMMUNITION", "Pioneer", "PITBULL TACTICAL", "Pittman Game Calls", "Plano", "Plano Molding Company", "PMC", "PMC AMMUNITION", "Pointer", "Polymer 80 Inc", "Power Belt Bullets", "PowerBelt Bullets", "PPU", "Predator Tactics", "PREDATOR TACTICS INC", "PREMIER BODY ARMOR LLC", "Primary Weapons", "Primary Weapons Systems", "Primos", "Pro Ears", "Pro Mag", "Pro-Shot", "Pro-Shot Products", "ProMag", "Promag Mfg. Inc.", "Prometheus Group Llc/Browning Camera", "PROOF RESEARCH", "Protektor Model", "PRT", "Prvi Partizan", "PS Products", "PSE Archery", "PSP", "PSP Products", "PTR", "Pulsar", "Pulsar Thermal", "Pyramex", "Pyramex Safety", "Q LLC", "QAD", "Quake", "Quaker Boy", "QUIETKAT INC /VISTA OUTDO", "Radian Weapons", "Radians", "Radians Inc.", "Radical Firearms", "Radio Systems Corporation dba Sport Dog", "RAGE", "Ram-Line", "Ramcat", "RamRodz", "Ramshot", "Ramshot Powder", "Ranch Products", "RANGER RUGGED GEAR", "RANGETRAY, LLC", "Rapid Force", "Rapid Rope", "RAPID ROPE LLC", "RapidPure", "Raven Concealment Systems", "Ravenwood International", "Ravin Crossbows", "Rayovac", "RCBS", "Real Avid", "Real Avid/Revo", "Realtree", "Reapr", "RECOVER INNOVATIONS INC", "Red Army Standard", "Red Rock Gear", "Redball Sports", "Redding Reloading Equipment", "Redfield", "Redfield Mounts", "REFURBISH DENT SCRATCH", "REM ARMS LLC ACCESS", "REM ARMS LLC FIREARMS", "Remington", "Remington Accessories", "Remington Ammunition", "Remington Arms Co. Inc.", "Remington Bulk Components", "Remington Cutlery", "Reptilia", "REPTILLA,LLC", "Retay USA", "Rex Enterprises - Rambo Bikes", "Rhino", "Rhino Blinds", "Rifle Basix", "RIG", "Riley Defense", "RILEY DEFENSE INC", "RIO AMMUNITION", "Rise Armament", "Riton", "Riton Optics", "Rival Arms", "Rivers Edge", "Rizzini", "RIZZINI USA", "RNT Calls", "Rock Isand Armory", "Rock River Arms", "Rocket Broadheads", "Rockwood Corp/Speedwell", "Rocky Mountain", "Rocky Mountain Hunting Calls", "Rosco Manufacturing", "Rossi", "RS Regulate", "Ruger / Sturm, Ruger & Co.", "Ruger Air Guns", "Rugged Gear", "Rugged Rare", "RUGGED SUPPRESSOR", "Rugged Suppressors", "RUKX GEAR", "RW Minis", "RWS", "S", "S.O.G", "S&W Pepper Spray", "Sabre", "Safariland", "Safariland / Break Free", "Safariland / Safariland", "Sako", "SAKO (BERETTA USA)", "Samson", "Samson Manufacturing Corp.", "San Tan Tactical", "SAR Arms", "Sarge Knives", "Savage", "Sawyer", "SB Tactical", "SCCY Industries", "Scent Thief", "Scentcrusher", "Scentlok", "Schrade", "Scopecoat", "Scott Archery", "Scout", "SDS Imports", "SDS IMPORTS LLC", "Seal 1", "Security Equipment Corporation", "Seekins Precision", "Sellier & Bellot", "Sellmark Corporation", "SENCUT", "Sentry", "Sentry Products Group DBA Scopecoat", "SGM Tactical", "Shadow Systems", "Shark Coast Tactical", "SHARPS BROS LLC", "Sharps Bros.", "Sheffield", "Shield Arms", "Shield Sights", "Shooter's Choice", "Shooter's World", "Shooters Choice", "Shooters Choice LLC", "Shooters Ridge", "Shooting Made Easy", "Sierra", "Sierra Bullets", "Sig Sauer", "Sig Sauer Airguns", "Sig Sauer Electro-Optics", "Sightmark", "Sightron", "SilencerCo", "Simmons", "Sims Vibration Laboratories", "SINTERFIRE INC", "Sionyx", "SKB", "SKB Sports", "Skull Hooker", "Slick Trick", "Slip 2000", "SLIP 2000 (SPS MARKETING)", "Slogan Outdoors", "Smart Reloader (Helvetica Trad", "SME", "SME Products", "Smith & Wesson / S&W", "Smith & Wesson LE", "Smiths Products", "Snap Safe", "SnapSafe", "SOG", "SOG Knives & Tools", "Sog Specialty Knives Inc.", "Sons of Liberty Gun Works", "Southern Bloomer", "Southern Bloomer Mfg. Co.", "Southern Bloomers", "Spartan", "Spartan Camera", "Speer", "Speer Ammo", "Speer Ammunition", "Speer Bullets", "Sphinx", "Spike's Tactical", "Spikes", "SportDog", "Sports Afield", "Spot Hogg", "Springfield Armory", "Spyderco", "Spypoint", "SRM Arms", "Stack-On", "Stack-On Products Co.", "Stag Arms", "Standard Manufacturing", "Standard Mfg", "Starline Brass", "Stars and Stripes Defense Ammunition", "Stealth Cam", "Stealth Operator", "Stealth Operator Holster", "Stearns Inc.", "STEEL RIVER KNIVES", "Steiner", "Stern Defense", "Steyr Mannlicher", "Sticky Holsters", "STREAK Ammunition", "Streamlight", "Strike", "Strike Industries", "Striker", "Strongsuit", "STV Technology", "Summit", "Summit Tree Stands", "SUPPRESS TEC LLC", "Surefire", "Surefire Llc", "SURELOCK (DANSONS US LLC)", "Surelock Safe LLC", "Surgeon", "SW Knives", "Swab-Its", "Swagger", "SWAGGER LLC", "Swhacker", "Swift Bullet Company", "Sylvan Arms", "SZCO Rite Edge", "SZCO Sawmill", "SZCO Sierra Zulu", "SZCO Steel Stag", "T R Imports", "T.R.U. Ball Archery", "T/C Accessories", "Tac Shield", "TAC VANES", "TacFire", "Tacfire Inc.", "TACSHIELD (MILITARY PROD)", "TacStar", "Tactacam", "Tactaload L.L.C.", "Tactica", "Tactical Innovations", "Tactical Solutions", "Tactical Superiority", "Tagua", "Tagua Gun Leather", "Talley", "Talley Manufacturing", "Talley Mounting Systems", "Talon", "TALON ARMAMENT LLC", "TALON Grips Inc", "Tanfoglio", "TangoDown", "TANNERITE", "Tapco", "TARA TACTICAL USA", "Target Sports", "Tasco", "Taser", "Taser International Inc. - AXON", "Taurus", "Taylors and Company", "Techna Clip", "Techna Clips", "TekMat", "Teledyne Flir Commercial Systems Inc", "TEMPLAR KNIFE", "TEN POINT", "Tenpoint Crossbow Technologies", "Tenzing", "Tethrd LLC", "Tetra", "TEXAS AMMO INC", "The Allen Company Inc.", "The Outdoor Connection", "Thermacell", "Thermacell Repellents Inc.", "Thermold", "Thompson Center", "Thorn Broadheads", "Thril", "THRIL INC", "Tight Spot", "Tikka", "Tikka T3", "TIMBER CREEK OUTDOOR INC", "Timney", "Timney Triggers", "Tinks", "Tippman Arms", "Tipton", "Tisas", "Titan 3D", "TNW Firearms", "TNW FIREARMS INC", "Tokarev", "Tokarev Shotguns", "TOOLMAN TACTICAL, INC", "Top Brass", "Top Brass Inc.", "TOP BRASS LLC", "Tornado Personal Defense", "Torrent Suppressors", "TPS Arms", "TR Imports", "TR&Z", "Traditions Performance Firearms", "Trailblazer Firearms", "TRIGGERTECH", "Trijicon", "Trijicon Electro Optics", "Trijicon EO", "TrijiconInc.", "TriStar Arms", "Trius", "Trophy Ridge", "Trophy Taker", "Troy Defense", "Troy Ind", "Troy Industries", "Troy Industries Inc", "True Precision", "Trufire", "Truglo", "Truglo Bowfishing", "Truglo Inc.", "Tulammo", "Turner Fabrications", "Two Two Three Innovations", "TXAT - DBA Aguila Ammunition", "TYPHOON DEFENSE (ARMSCO)", "U.S. OPTICS", "Uct Coatings Inc. Fail Zero", "UDAP", "Ulfhednar", "Ultimate Survival Technologies", "Ultradot", "Ultradyne", "Ultradyne USA", "Umarex", "Umarex USA", "Unbranded AR", "Uncle Bud'S Css", "Uncle Henry", "Uncle Mike's", "Uncle Mikes", "UNCLE MIKES-LEATHER(1791)", "Underwood Ammo", "Unity Tactical", "US Optics", "US Palm", "US PeaceKeeper", "UST - Ultimate Survival Technologies", "UTAS-USA", "UTG", "UTG Pro", "UTS/PEPPERBALL", "Uzi Accessories", "Vanguard", "VaporTrail", "VELVET ANTLER TECH", "VersaCarry", "Versacarry By Sitzco Llc.", "Vertx", "Victory Archery", "Viking Tactics Inc.", "Viper Archery Products", "Viridian", "Viridian Green Laser", "Viridian Weapon Technologies", "Volquartsen Custom", "Walker's", "Walkers", "Walkers Game Ear", "Walther Air Pistol", "Walther Arms", "Warne", "Warne Manufacturing Company", "Warne Scope Mounts", "Wasp Archery Products", "Watchtower Firearms", "WE Knife", "Weatherby Ammunition", "Weatherby Inc", "Weaver", "Weaver Mounts", "Western Rivers", "Wheeler", "Whitetail Institute", "Whitewater Outdoors", "Wicked Ridge", "Wicked Tree Gear", "WILDGAME INNOVATIONS GSM", "Wildlife Research", "Wildlife Research Center", "Williams Gunsight Co.", "Wilson Combat", "Winchester", "Winchester Ammo", "Winchester Bulk Components", "Winchester Guns", "Winchester Muzzleloading", "Winchester Powder", "Winchester Safes", "Windham Weaponry", "Wise Foods", "WMD Guns", "Wolf Performance Ammunition", "Woodhaven Calls", "WOODHAVEN CUSTOM CALLS", "Woolrich Elite Series", "Woox", "WOOX LLC", "Wraithworks", "X-GRIP", "X-VISION", "XPEDITION ARCHERY LLC", "Xpedition Crossbows", "XS Sight Systems", "Xs Sight Systems Inc.", "XS Sights", "Yankee Hill", "Yankee Hill Machine", "Yankee Hill Machine Co", "Zac Brown's Southern Grind", "Zaffiri Precision", "Zap", "Zastava Arms", "Zenith Firearms", "ZEV", "Zev Technologies", "ZRODELTA"];
                                    /*
                                    fetch("https://ffl-api.garidium.com", {
                                        method: "POST",
                                        headers: {
                                        "Accept": "application/json",
                                        "Content-Type": "application/json",
                                        "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>",
                                        },
                                        body: JSON.stringify({"action": "get_product_restriction_options"})
                                    })
                                    .then(response=>response.json())
                                    .then(data=>{
                                        try{
                                            data.forEach(item => {
                                                if (item.type == "brand"){
                                                    if (!all_brands.includes(item.value)) {
                                                        all_brands.push(item.value);
                                                    }
                                                }
                                                if (item.type == "category"){
                                                    if (!all_categories.includes(item.value)) {
                                                        all_categories.push(item.value);
                                                    }
                                                }
                                                if (item.type == "product_class"){
                                                    if (!all_product_classes.includes(item.value)) {
                                                        all_product_classes.push(item.value);
                                                    }
                                                }
                                            });
                                        } catch (error) {
                                            alert("No product restrictions found for this key");
                                        }
                                    });
                                    */

                                    var options = {
                                        modes: ['text', 'code', 'tree', 'form', 'view'],
                                        mode: 'tree',
                                        ace: ace,
                                        schema: {
                                            "$schema": "http://json-schema.org/draft-04/schema#",
                                            "title": "g-FFL Cockpit Configuration",
                                            "description": "Configuration file for the g-FFL Cockpit WooCommerce Plugin",
                                            "type": "object",
                                            "required": ["distributors", "max_distributor_cost",
                                                         "max_listing_count", "min_distributor_cost",
                                                         "min_quantity_to_list", "notification_email",
                                                         "pricing", "product_restrictions"],
                                            "properties": {
                                                "max_listing_count": {
                                                    "description": "Maximum Listing Count",
                                                    "type": "integer",
                                                    "exclusiveMinimum": true,
                                                    "minimum":0
                                                },
                                                "notification_email": {
                                                    "description": "Email for System Notifications and Alerts",
                                                    "type": "string",
                                                    "format": "email"
                                                },
                                                "max_distributor_cost": {
                                                    "description": "Maximum Distributor Cost for a Listed Item",
                                                    "type": "number"
                                                },
                                                "min_distributor_cost": {
                                                    "description": "Minimum Distributor Cost for a Listed Item",
                                                    "type": "number"
                                                },
                                                "min_quantity_to_list": {
                                                    "description": "Minimum In-Stock Quantity to List an Item",
                                                    "type": "integer"
                                                },
                                                "product_restrictions": {
                                                    "description": "Product Restrictions",
                                                    "$ref": "#/definitions/product_restrictions"
                                                },
                                                "pricing": {
                                                    "title": "Pricing",
                                                    "description": "Pricing and Margin Configuration",
                                                    "$ref": "#/definitions/pricing"
                                                },
                                                "distributors": {
                                                    "description": "Firearms and Accessories Distributors",
                                                    "type": "object",
                                                    "properties": {
                                                        "Lipseys": {
                                                            "description": "Lipseys Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_lipseys"
                                                        },
                                                        "Zanders": {
                                                            "description": "Zanders Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_zanders"
                                                        },
                                                        "Davidsons": {
                                                            "description": "Davidsons Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_davidsons"
                                                        },
                                                        "RSR Group": {
                                                            "description": "RSR Group Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_rsr"
                                                        },
                                                        "2nd Amendment Wholesale": {
                                                            "description": "2nd Amendment Wholesale Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_twoaw"
                                                        },
                                                        "Chattanooga Shooting Supplies": {
                                                            "description": "Chatanooga Shooting Supplies Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_cssi"
                                                        },
                                                        "Sports South": {
                                                            "description": "Sports South Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_tsw"
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "Chatanooga Shooting Supplies"
                                                            ]
                                                        },                 
                                                        {
                                                            "required": [
                                                                "2nd Amendment Wholesale"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "RSR Group"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Zanders"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Davidsons"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Lipseys"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "targets": {
                                                    "title": "Targets",
                                                    "description": "Targets to import products to, such as woocommerce, ammoseek or gunbroker",
                                                    "type": "object",
                                                    "additionalProperties": false,
                                                    "properties": {
                                                        "woo": {
                                                            "description": "WooCommerce Product Feed Settings",
                                                            "$ref": "#/definitions/target_woocommerce"
                                                        },
                                                        "ammoseek": {
                                                            "description": "AmmoSeek Product Feed Settings",
                                                            "$ref": "#/definitions/target_rss"
                                                        },
                                                        "gun.deals": {
                                                            "description": "Gun.Deals Product Feed Settings",
                                                            "$ref": "#/definitions/target_rss"
                                                        },
                                                        "gunbroker": {
                                                            "description": "Gunbroker Product Feed Settings",
                                                            "$ref": "#/definitions/target_gunbroker"
                                                        }
                                                    }
                                                },
                                            },
                                            "definitions":
                                            {
                                                "include_exclude": {
                                                    "title": "Include Exclude Options",
                                                    "description": "Include Exclude Options",
                                                    "type": "object",
                                                    "properties": {
                                                        "exclude": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "include": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        }
                                                    },"anyOf": [
                                                        {
                                                            "required": [
                                                                "exclude"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "include"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "include_exclude_product_class": {
                                                    "title": "Include Exclude Product Class Options",
                                                    "description": "Include Product Class Exclude Options",
                                                    "type": "object",
                                                    "properties": {
                                                        "exclude": {
                                                            "$ref": "#/definitions/product_classes"
                                                        },
                                                        "include": {
                                                            "$ref": "#/definitions/product_classes"
                                                        }
                                                    },"anyOf": [
                                                        {
                                                            "required": [
                                                                "exclude"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "include"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "brands": {
                                                    "title": "Brand Selections",
                                                    "description": "Brand Selections",
                                                    "type": "array",
                                                    "items": {
                                                        "type": "string",
                                                        "enum": all_brands
                                                    }
                                                },
                                                "product_classes": {
                                                    "title": "Product Class Selections",
                                                    "description": "Product Class  Selections",
                                                    "type": "array",
                                                    "items": {
                                                        "type": "string",
                                                        "enum": ["AC","AG","AO","AP","AR","BP","FA","FI","FP","HT","HZ","KN","MG","MZ","OP","OT","RL","SO"]
                                                    }
                                                },
                                                "include_exclude_brand": {
                                                    "title": "Include Exclude Brand Otions",
                                                    "description": "Include Exclude Brand Options",
                                                    "type": "object",
                                                    "properties": {
                                                        "exclude": {
                                                            "$ref": "#/definitions/brands"
                                                        },
                                                        "include": {
                                                            "$ref": "#/definitions/brands"
                                                        }
                                                    },"anyOf": [
                                                        {
                                                            "required": [
                                                                "exclude"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "include"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "gunbroker_shipping_profile": {
                                                    "title": "Gunbroker Shipping Profile",
                                                    "description": "Gunbroker Shipping Profile",
                                                    "type": "object",
                                                    "properties": {
                                                        "profile_id": {
                                                            "type": "number"
                                                        },
                                                        "distributors": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string",
                                                                "enum": ["LIP","ZND","CSSI","2AW","DAV","RSR","TSW"]
                                                            }
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                     "required": [
                                                        "profile_id"
                                                    ]
                                                },
                                                "alerts": {
                                                    "title": "Alert Configuration",
                                                    "description": "Alert Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "change_threshold_percent": {
                                                            "type": "number",
                                                            "minimum":0,
                                                            "maximum":0.99
                                                        },
                                                        "delete_threshold_percent": {
                                                            "type": "number",
                                                            "minimum":0,
                                                            "maximum":0.99
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "change_threshold_percent"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "delete_threshold_percent"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "product_restrictions":{
                                                    "properties": {
                                                        "sku": {
                                                            "$ref": "#/definitions/include_exclude"
                                                        },
                                                        "upc": {
                                                            "$ref": "#/definitions/include_exclude"
                                                        },
                                                        "brand": {
                                                            "$ref": "#/definitions/include_exclude_brand"
                                                        },
                                                        "category": {
                                                            "$ref": "#/definitions/include_exclude"
                                                        },
                                                        "product_class": {
                                                            "$ref": "#/definitions/include_exclude_product_class"
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "product_class"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "sku"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "category"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "brand"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "upc"
                                                            ]
                                                        } 
                                                    ]
                                                },
                                                "pricing": {
                                                    "properties": {
                                                        "margin": {
                                                            "properties": {
                                                                "default": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "firearms": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "ammunition": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "accessories": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom1": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom2": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom3": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom4": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom5": {
                                                                    "$ref": "#/definitions/margin"
                                                                }
                                                            }
                                                        },
                                                        "sales_tax_assumption": {
                                                            "type": "number",
                                                            "minimum":0,
                                                            "maximum":0.99
                                                        },
                                                        "credit_card_fee_percent": {
                                                            "type": "number",
                                                            "minimum": 0,
                                                            "maximum": 0.99
                                                        },
                                                        "round_to_nearest_dollar": {
                                                            "type": "boolean"
                                                        },
                                                        "include_shipping_in_price": {
                                                            "type": "boolean"
                                                        },
                                                        "credit_card_fee_transaction": {
                                                            "type": "number"
                                                        },
                                                        "include_credit_card_fees_in_price": {
                                                            "type": "boolean"
                                                        }
                                                    },
                                                    "required": [
                                                        "credit_card_fee_percent",
                                                        "credit_card_fee_transaction",
                                                        "include_credit_card_fees_in_price",
                                                        "include_shipping_in_price",
                                                        "margin",
                                                        "round_to_nearest_dollar",
                                                        "sales_tax_assumption"
                                                    ]
                                                },
                                                "margin": {
                                                    "properties": {
                                                        "sku": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "upc": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "brand": {
                                                            "$ref": "#/definitions/brands"
                                                        },
                                                        "category": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "product_class": {
                                                            "$ref": "#/definitions/product_classes"
                                                        },
                                                        "margin_dollar": {
                                                            "type": "number"
                                                        },
                                                        "margin_percentage": {
                                                            "type": "number",
                                                            "minimum": 0,
                                                            "maximum": 0.99
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "margin_dollar"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "margin_percentage"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "flat_rate_shipping": {
                                                    "title": "Flat Rate Shipping",
                                                    "description": "Flat Rate Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "cost": {
                                                            "type": "number"
                                                        },
                                                        "shipping_class": {
                                                            "type": "string"
                                                        }
                                                    },
                                                    "required": [
                                                        "cost",
                                                        "shipping_class"
                                                    ]
                                                },
                                                "price_based_shipping_levels": {
                                                    "type": "array",
                                                    "items": {
                                                        "$ref": "#/definitions/price_based_shipping"
                                                    }
                                                },
                                                "price_based_shipping": {
                                                    "title": "Price-based Shipping",
                                                    "description": "Price-based Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "flat_rate": {
                                                            "$ref": "#/definitions/flat_rate_shipping"
                                                        },
                                                        "max_unit_cost": {
                                                            "type": "integer"
                                                        },
                                                        "min_unit_cost": {
                                                            "type": "integer"
                                                        }
                                                    },
                                                    "required": [
                                                        "flat_rate",
                                                        "min_unit_cost"
                                                    ]
                                                },
                                                "shipping": {
                                                    "title": "Shipping",
                                                    "description": "Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "price_based_shipping_levels": {
                                                            "$ref": "#/definitions/price_based_shipping_levels"
                                                        },
                                                        "flat_rate": {
                                                            "$ref": "#/definitions/flat_rate_shipping"
                                                        }
                                                    },
                                                    "oneOf": [
                                                        {
                                                            "required": [
                                                                "price_based_shipping"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "flat_rate"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "target_rss": {
                                                    "title": "RSS Feed",
                                                    "description": "RSS Feed Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "listed_products": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "guns": {
                                                                    "description": "Guns Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"        
                                                                },
                                                                "brass": {
                                                                    "description": "Brass Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "powder": {
                                                                    "description": "Powder Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "bullets": {
                                                                    "description": "Bullets Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "primers": {
                                                                    "description": "Primers Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "magazines": {
                                                                    "description": "Magazine Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "ammunition": {
                                                                    "description": "Ammunition Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "reloading": {
                                                                    "description": "Reloading Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "reloading_misc": {
                                                                    "description": "Miscellaneous Reloading Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "parts": {
                                                                    "description": "Parts Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "other": {
                                                                    "description": "Other Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                }
                                                            },
                                                            "anyOf": [
                                                                {
                                                                    "required": [
                                                                        "guns"
                                                                    ]
                                                                },                 
                                                                {
                                                                    "required": [
                                                                        "brass"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "powder"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "bullets"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "primers"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "magazines"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "ammunition"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "reloading"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "reloading_misc"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "parts"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "other"
                                                                    ]
                                                                }
                                                            ],
                                                            "title": "Listed Products"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "listed_products"
                                                    ]
                                                },
                                                "target_woocommerce": {
                                                    "title": "WooCommerce",
                                                    "description": "WooCommerce Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "url": {
                                                            "type": "string",
                                                            "format": "uri",
                                                            "qt-uri-protocols": [
                                                                "https", "http"
                                                            ]
                                                        },
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "pricing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "margin_adjustment_dollar": {
                                                                    "type": "number"
                                                                },
                                                                "margin_adjustment_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            }
                                                        },
                                                        "consumer-key": {
                                                            "type": "string"
                                                        },
                                                        "consumer-secret": {
                                                            "type": "string"
                                                        },
                                                        "load_batch_count": {
                                                            "description": "Number of products to load in each thread, suggested at 10.",
                                                            "type": "integer"
                                                        },
                                                        "product_restrictions": {
                                                            "description": "Product Restrictions",
                                                            "$ref": "#/definitions/product_restrictions"
                                                        },
                                                        "alerts": {
                                                            "description": "Alert Configurations",
                                                            "$ref": "#/definitions/alerts"
                                                        },
                                                        "manage_product_categories": {
                                                            "type": "boolean"
                                                        },
                                                        "manage_product_attributes": {
                                                            "type": "boolean"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "consumer-key",
                                                        "consumer-secret",
                                                        "load_batch_count",
                                                        "manage_product_categories",
                                                        "manage_product_attributes",
                                                        "pricing",
                                                        "url"
                                                    ]
                                                },
                                                "target_gunbroker": {
                                                    "title": "Gunbroker",
                                                    "description": "Gunbroker Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "key": {
                                                            "type": "string",
                                                            "format": "uuid"
                                                        },
                                                        "environment": {
                                                            "type": "string",
                                                            "enum": ["sandbox","production"]
                                                        },
                                                        "shipping_profiles": {
                                                            "description": "Gunbroker Shipping Profiles",
                                                            "title": "Shipping Profiles",
                                                            "type": "object",
                                                            "properties": {},
                                                            "patternProperties": {
                                                                "[a-zA-Z0-9_ ]*": {
                                                                    "$ref": "#/definitions/gunbroker_shipping_profile"
                                                                }
                                                            }
                                                        },
                                                        "fees": {
                                                            "description": "Gunbroker Fees",
                                                            "title": "Fees",
                                                            "type": "object",
                                                            "properties": {
                                                                "compliance": {
                                                                    "type": "number"
                                                                },
                                                                "tier_1_dollar": {
                                                                    "type": "integer"
                                                                },
                                                                "tier_1_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                },
                                                                "tier_2_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            },
                                                            "required": [
                                                                "compliance",
                                                                "tier_1_dollar",
                                                                "tier_1_percent",
                                                                "tier_2_percent"
                                                            ]
                                                        },
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "pricing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "margin_adjustment_dollar": {
                                                                    "type": "number"
                                                                },
                                                                "margin_adjustment_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            }
                                                        },
                                                        "password": {
                                                            "type": "string"
                                                        },
                                                        "username": {
                                                            "type": "string"
                                                        },
                                                        "payment_methods": {
                                                            "title": "PaymentMethods",
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "COD": {
                                                                    "type": "boolean"
                                                                },
                                                                "Amex": {
                                                                    "type": "boolean"
                                                                },
                                                                "check": {
                                                                    "type": "boolean"
                                                                },
                                                                "Escrow": {
                                                                    "type": "boolean"
                                                                },
                                                                "PayPal": {
                                                                    "type": "boolean"
                                                                },
                                                                "Discover": {
                                                                    "type": "boolean"
                                                                },
                                                                "MoneyOrder": {
                                                                    "type": "boolean"
                                                                },
                                                                "FreedomCoin": {
                                                                    "type": "boolean"
                                                                },
                                                                "SeeItemDesc": {
                                                                    "type": "boolean"
                                                                },
                                                                "CertifiedCheck": {
                                                                    "type": "boolean"
                                                                },
                                                                "USPSMoneyOrder": {
                                                                    "type": "boolean"
                                                                },
                                                                "VisaMastercard": {
                                                                    "type": "boolean"
                                                                }
                                                            },
                                                            "required": [
                                                                "Amex",
                                                                "COD",
                                                                "CertifiedCheck",
                                                                "Discover",
                                                                "Escrow",
                                                                "FreedomCoin",
                                                                "MoneyOrder",
                                                                "PayPal",
                                                                "SeeItemDesc",
                                                                "USPSMoneyOrder",
                                                                "VisaMastercard",
                                                                "check"
                                                            ]
                                                        },
                                                        "from_postal_code": {
                                                            "type": "string"
                                                        },
                                                        "listing_duration": {
                                                            "type": "integer"
                                                        },
                                                        "picture_flairing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "active": {
                                                                    "type": "boolean"
                                                                },
                                                                "border": {
                                                                    "type": "object",
                                                                    "additionalProperties": false,
                                                                    "properties": {
                                                                        "color": {
                                                                            "type": "string"
                                                                        },
                                                                        "active": {
                                                                            "type": "boolean"
                                                                        },
                                                                        "pixel_width": {
                                                                            "type": "integer"
                                                                        }
                                                                    },
                                                                    "required": [
                                                                        "active",
                                                                        "color",
                                                                        "pixel_width"
                                                                    ],
                                                                    "title": "Border"
                                                                }
                                                            },
                                                            "required": [
                                                                "active"
                                                            ],
                                                            "title": "Picture Flairing"
                                                        },
                                                        "standard_text_id": {
                                                            "type": "integer"
                                                        },
                                                        "inspection_period": {
                                                            "type": "integer"
                                                        },
                                                        "product_restrictions": {
                                                            "description": "Product Restrictions",
                                                            "$ref": "#/definitions/product_restrictions"
                                                        },
                                                        "standard_description_html": {
                                                            "type": "string",
                                                            "description": "Text that appears in all Gunbroker Listings"
                                                        },
                                                        "drive_customers_to_website": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "active": {
                                                                    "type": "boolean"
                                                                },
                                                                "message": {
                                                                    "description": "This message will appear above the listing to prompt the user to go to your website.",
                                                                    "type": "string"
                                                                },
                                                                "website_banner": {
                                                                    "description": "This is a place to put a image banner area, or some other branding material.",
                                                                    "type": "string"
                                                                },
                                                                "product_qr_codes": {
                                                                    "type": "object",
                                                                    "additionalProperties": false,
                                                                    "properties": {
                                                                        "active": {
                                                                            "type": "boolean"
                                                                        },
                                                                        "icon_url": {
                                                                            "type": "string",
                                                                            "format": "uri",
                                                                            "qt-uri-protocols": [
                                                                                "https", "http"
                                                                            ]
                                                                        }
                                                                    },
                                                                    "required": [
                                                                        "active",
                                                                        "icon_url"
                                                                    ],
                                                                    "title": "Product QR Codes"
                                                                }
                                                            },
                                                            "required": [
                                                                "active"
                                                            ],
                                                            "title": "DriveCustomersToWebsite"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "fees",
                                                        "from_postal_code",
                                                        "inspection_period",
                                                        "listing_duration",
                                                        "password",
                                                        "payment_methods",
                                                        "pricing",
                                                        "standard_description_html",
                                                        "standard_text_id",
                                                        "environment",
                                                        "username"
                                                    ]
                                                },
                                                "distributor_tsw": {
                                                    "title": "Sports South",
                                                    "description": "Sports South Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "TSW"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_cssi": {
                                                    "title": "Chattanooga Shooting Supplies",
                                                    "description": "Chattanooga Shooting Supplies Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "CSSI"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_davidsons": {
                                                    "title": "Davidsons",
                                                    "description": "Davidsons Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "DAV"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_lipseys": {
                                                    "title": "Lipseys",
                                                    "description": "Lipseys Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "LIP"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_rsr": {
                                                    "title": "RSR Group",
                                                    "description": "RSR Group Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "RSR"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_twoaw": {
                                                    "title": "2nd Amendment Wholesale",
                                                    "description": "2nd Amendment Wholesale Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "2AW"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_zanders": {
                                                    "title": "Zanders",
                                                    "description": "Zanders Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "ZND"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                }
                                            }
                                        }
                                    }
                                    var editor = new JSONEditor(document.getElementById("jsoneditor"), options);
                                    editor.set({"Loading Configuration": "Please wait..."});
                                    window.onload = function(){
                                        get_and_set_cockpit_configuration("<?php echo esc_attr($gFFLCockpitKey);?>", false);
                                        if (window.location.host == 'garidium.com' || window.location.host == 'localhost:8000'){
                                            document.getElementById('g-ffl-admin-buttons').style.display = '';
                                        }
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
                        <div id="submit_button_div">
                            <?php submit_button(); ?>
                        </div>
                        <br>
                        <table style="border: solid black 1px;">
                            <tr style="background-color:#EEEEEE;weight:bold;font-style:italic;"><td colspan=2>Product Class Code Reference</td></tr>
                            <tr><td><b>AC</b></td><td>Accessories</td></tr>
                            <tr><td><b>AG</b></td><td>Air Guns & Accessories</td></tr>
                            <tr><td><b>AO</b></td><td>Ammunition</td></tr>
                            <tr><td><b>AP</b></td><td>Apparel</td></tr>
                            <tr><td><b>AR</b></td><td>Archery</td></tr>
                            <tr><td><b>BP</b></td><td>Black Powder Firearms</td></tr>
                            <tr><td><b>FA</b></td><td>Firearms</td></tr>
                            <tr><td><b>FI</b></td><td>Fishing</td></tr>
                            <tr><td><b>FP</b></td><td>Firearms Parts</td></tr>
                            <tr><td><b>HT</b></td><td>Hunting</td></tr>
                            <tr><td><b>HZ</b></td><td>Hazardous</td></tr>
                            <tr><td><b>KN</b></td><td>Knives</td></tr>
                            <tr><td><b>MG</b></td><td>Magazines</td></tr>
                            <tr><td><b>MZ</b></td><td>Muzzleloading</td></tr>
                            <tr><td><b>OP</b></td><td>Optics</td></tr>
                            <tr><td><b>OT</b></td><td>Other</td></tr>
                            <tr><td><b>RL</b></td><td>Reloading</td></tr>
                            <tr><td><b>SO</b></td><td>SOT</td></tr>
                        </table>
                        <a style="cursor:pointer;" onclick="document.getElementById('white_label_settings_name').style.display='';document.getElementById('white_label_settings_url').style.display='';">&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;</a>
                    </form>
                </div>
            </div>
            <div id="product_feed" class="tabcontent">
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="product_feed_table"></div>
                    <div style="padding:5px;"><button id="download_inventory_button" class="button alt" data-marker-id="">Download Inventory</button></div>
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
                            }
                            return "";
                        }
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var product_grid = new gridjs.Grid({
                            columns: [
                                //formatter: (_, row) => `${row.cells[0].data?row.cells[2].data + '*':row.cells[2].data}`
                                //{sort: false, name: "List", width: '50px', formatter: (cell) => `${cell?"Y":"N"}`}, 
                                {name: 'Dist', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[0].data)}">`)
                                },
                                {name: 'SKU'}, 
                                {sort: false, name: 'Product Image',
                                    formatter: (_, row) => gridjs.html(`<a style="cursor:pointer;" onclick="load_product_data('${row.cells[3].data.replace("\"","&quot;") + "','" + row.cells[0].data + "','" + row.cells[1].data + "','" + row.cells[2].data[0]['src']}')"><img style="max-height:40px;max-width:100px;height:auto;width:auto;" src="${row.cells[2].data[0]['src']}"></a>`)
                                },
                                {name: 'Name', width: '200px'}, 
                                {name: "UPC"},
                                {name: "MFG"},
                                {name: "MPN"},
                                {name: "Qty", width: '55px'},
                                {name: 'Cost', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                //{name: 'MAP', width: '80px', formatter: (cell) => `${(cell==null || cell == 0)?'':'$'+cell.toFixed(2)}`}, 
                                {name: 'Ship', width: '60px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                {name: 'Total', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                //{name: "MPN", width: '150px'},
                                //{name: "Category", width: '120px'},
                                //{name: 'Price', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`},   
                                //{name: 'DS', width: '50px'}
                            ],
                            sort: {
                                multiColumn: false,
                                server: {
                                    url: (prev, columns) => {
                                        if (!columns.length) return prev;
                                        const col = columns[0];
                                        const dir = col.direction === 1 ? 'asc' : 'desc';
                                        let colName = ['distid', 'distsku', 'distsku', 'name', 'upc', 'mfg_name', 'mpn', 'qty_on_hand', 'unit_price', 'shipping_cost', 'total_cost'][col.index];
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
                            height: window_height,
                            //width: '1500px',
                            style: {
                                td: {
                                    'padding': '3px'
                                },
                                th: {
                                    'padding': '3px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com/product?action=get_filtered_catalog',
                                method: 'GET',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
			                    },
                                then: data => JSON.parse(data).products.map(product => [//product.is_best_item,
                                                                   product.distid, 
                                                                   product.distsku,
                                                                   JSON.parse(product.images), // # change to image
                                                                   product.name,
                                                                   product.upc, 
                                                                   product.mfg_name, //+ ' (' + product.mpn + ")",
                                                                   product.mpn,
                                                                   product.qty_on_hand, 
                                                                   product.unit_price,  
                                                                   product.shipping_cost,
                                                                   product.total_cost]),  
                                                                   //product.drop_ship_flg,
                                                                   //product.map_price, 
                                                                   //product.item_cat,                                          
                                                                   //product.price])                                                            
                                                                   //product.drop_ship_flg])
                                total: data => JSON.parse(data).count
                            } 
                        });
                    
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
                                    <!-- The Modal -->
                    <div id="myModal" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <div align="center" id="product_detail_div"></div>
                        </div>
                        <script>
                            // Get the modal
                            var modal = document.getElementById("myModal");

                            // Get the button that opens the modal
                            var btn = document.getElementById("myBtn");

                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];

                            function load_product_data(title, distributor, sku, img_url){
                                //alert(data);
                                modal.style.display = "block";
                                document.getElementById("product_detail_div").innerHTML = "<h3>" + title + "</h3><br><img width='75%' src='" + img_url + "'/><br><img width=75 src='" + get_distributor_logo(distributor) + "'/><br>" + sku;
                            }

                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function() {
                                modal.style.display = "none";
                            }

                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }
                        </script>
                    </div>

                </div>
            </div>
            <div id="fulfillment" class="tabcontent">
                <h3>Fulfillment History</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="order_fulfillment_table"></div>
                    <div style="padding:5px;display: flex;width: 100%;justify-content: space-between;align-items: flex-start;">
                        <div style="flex: 1;">
                            <button id="download_fulfillment_history_button" class="button alt" data-marker-id="">Download Fulfillment History</button>
                        </div>
                        <div id="gunbroker_processor_section" style="display:none;">
                            <input type="number" style="width: 120px;" name="gunbroker_order_id" id="gunbroker_order_id" class="input-input"/>
                            <button id="process_gunbroker_order_button" class="button alt" data-marker-id="">Process Gunbroker Order</button>
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
                                            return gridjs.html(row.cells[3].data);
                                        }else{
                                            return gridjs.html(`<a target=_blank href="${row.cells[12].data}">${row.cells[3].data}</a>`);
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
                                        if (row.cells[11].data == "delivered"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:green;">Delivered</span></a>`);
                                        }else if (row.cells[11].data == "return_to_sender"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:red;">Return to Sender</span></a>`);
                                        }else{
                                            if (row.cells[10].data != null) {
                                                return gridjs.html(`<a target=_blank href="${row.cells[10].data}">${row.cells[11].data==null?"In Transit":row.cells[11].data}</a>`);
                                            }else{
                                                return gridjs.html(row.cells[11].data);
                                            }
                                        }
                                    }
                                },
                                {name: "Order URL", hidden: true},
                                {name: "Tracking URL", hidden: true},
                                
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
                                                                   order.order_url
                                                                ]),  
                                                                 
                                total: data => JSON.parse(data).count
                            } 
                        });
                  
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

                        if (window.location.host == 'garidium.com' || window.location.host == 'localhost:8000'){
                            document.getElementById("gunbroker_processor_section").style.display="";
                            document.getElementById("process_gunbroker_order_button").addEventListener("click", function(){
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
                                    document.getElementById('process_gunbroker_order_button').innerText = 'Process Gunbroker Order';     
                                    if (data.Error != null && data.Error != undefined){
                                        alert(data.Error);
                                    }else{
                                        of_grid.forceRender();
                                    }
                                });
                            });
                        }

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
                                {name: 'Timestamp', width: '160px', formatter: (cell) => `${new Date(cell).toLocaleString()}`}, 
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
                                innerDiv.innerHTML = '<div style="width:390px;"><iframe class="rumble" width="370" height="208" src="' + data[i].url + '" frameborder="0" allowfullscreen></iframe><br><span style="width:99%;">' + data[i].title + '</span><br><span style="color:#a29f9f !important;width:99%;height:50px;">' + data[i].description + '</span></div>';
                                video_div.appendChild(innerDiv);
                            }
                        });
                    }
                </script>
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <div class="video_grid" id="training_videos"></div>
                </div>
            </div>    
        </div>

    <?php }
}

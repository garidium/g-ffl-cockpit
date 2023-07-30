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
                                    var all_brands = ["10 Ring","1791","1791 Gunleather","2 Monkey Trading","2 Monkey Trading llc.","2A Armament","30-06 Outdoors","3M/Peltor","A-Zoom","AAC - Advanced Armament Company","AAC (Advanced Armament)","Accu-Tac","AccuSharp","ACE","Action Target","Adams Arms","Adams Arms Llc","Adaptive Tactical","ADCO","ADCO Arms","Advanced Tech","Advantage Arms","Aero Precision","AeroPrecision","AGM Global Vision","Aguila","Aguila Ammunition","AimCam","AimShot","Aimtech","Akkar","Alex Pro Firearms","Alexander Arms","ALG Defense","Alien gear","Alien Gear Holsters","Allen","Altus Brands","Amend2","American Buffalo Knife & Tool","American Built Arms Company","American Classic","American Defense","American Defense Mfg.","American Hunter","American Precision Firearms","American Tactical","American Tactical Inc","American Tactical Inc-ATI","Ameriglo","Ammo Inc","Ammunition Storage Components","AMS Bowfishing","Anderson","Anderson Manufacturing","Andro Corp Industries","Angstadt Arms","Ani-Logics Outdoors","Anschutz","Antler King","Apex Tactical Specialties","APF Armory - Alex Pro Firearms","Apollo Custom|Glock","Aquamira","Ar-mor","ARB","Arctic Ice","Arctic Shield","Arex Defense","Arisaka Defense","ARMA ZEKA","Armalite","Armasight","Armaspec","Armsco","Armsco Inc  Orthos Arms Otus","Armscor","Armscor|Rock Island Armory","Arsenal","Arsenal Firearms","Arsenal, Inc.","Athlon","ATI","ATI Outdoors","ATN","Atomic Ammunition","Atsko","Auto Ordnance","Auto-Ordnance","Auto-Ordnance - Thompson","Avian X","Avidity Arms","Axcel","Axe","AXEON","B&T","B&T USA","B3 Archery","B5 Systems","Backup Tactical","Ballistic Advantage","Barnaul Ammunition","Barnes","Barrett","Barrett Firearms","Barska","Bastion","Battle Arms Development","Bear & Son","Bear Archery","Bear Edge","Bear Ops","Beartooth Products","Bee Stinger","Beeman","Benchmaster","Benjamin","Benjamin Sheridan","Beretta","Beretta U.S.A.","Beretta|Sako","Beretta|Tikka","Bergara","Bergara Rifles","Berger Bullets","Bersa","Bianchi","Big Bobber","Bino Dock","Birchwood Casey","Black Aces Tactical","Black Rain Ordnance","Black Rain Ordnance Inc","Black Spider LLC","BLACKHAWK","BlackPoint Tactical","Blaser Sauer USA","Blaser USA Inc.","Blazer Ammunition","Block Targets","Blue Book Publications","Blue Force Gear","Blue Line Diana","BLUE LINE GLOBAL","Blue Line Solutions","Bob Allen","Bog","Bohning","Bond Arms","Bond Arms Inc.","BoneView","Bootleg","BoreSnake","Bounty Hunter","Bravo Company","Bravo Company Mfg.","Bravo Concealment","BrazTech|Rossi","Break Free","BreakFree","Breakthrough Clean Technologies","Breakthrough cleaning","Brigade MFG","Browning","Browning Firearms","Browning/Winchester Repeating Arms","BSA","BSA Optics","Bubba Blade","Bubba Rope","Buck Bomb","Buck Wear","Buffalo Cartridge Company","Bulldog","Bulldog Cases","BulletSafe","Burris","Bushmaster","Bushmaster Firearms","Bushmaster Firearms Inc.","Bushnell","Butler Creek","Byrna Technologies","C Products Defense","C-More Systems","C&H Precision Weapons","CAA","Cajun Bowfishing","Caldwell","Camelbak","Cameleon","CANIK","Carbon Express","Carlson","CCI","CenterPoint","Century","Century Arms","CGS Group","Champion","Champion Traps & Targets","Chaos Gear Supply","Charles Daly","Charter Arms","Cheytac USA","Chiappa Firearms","Chiappa Firearms USA Ltd","CHILD GUARD","Chipmunk","Christensen Arms","Cimarron","Cimarron Fac","Citadel","CIVIVI","Cleanshot","Cloud Defensive","CMC Products","CMC Triggers","CMMG","Cmmg Inc.","Cobra Archery","Cobratec Knives","Code Blue","Cold Steel","Cole-TAC","Coleman","Colt","Colt Manufacturing","Colt's Manufacturing","Columbia River Knife & Tool","Comp-Tac","ConQuest","Cor-Bon","CorBon","Covert Camera's","Coyote Light","CRANFORD MFG.","Crickett","Crimson Trace","Crimson Trace Corporation","CRKT Knives","Crosman","Cross Industries","Crossfire","Crucial Concealment","Cruxord","Cuddeback","Custom Bow Equipment","CVA","Cyclops","CZ","CZ Custom","CZ-USA","Cz-Usa Firearms","CZ-USA|Dan Wesson","D-Lead","D.T. Systems","D&H Tactical","DAC","Daisy","Dan Wesson","Daniel Defense","Dead Air Armament","Dead Air Silencers","Dead Down Wind","Del-Ton","DeSantis","DeSantis Gunhide","Desert Eagle","Desert Tech","Diamondback Barrels","Diamondback Firearms","Diamondhead USA Inc.","Dickinson Arms","DNZ","DNZ Products","Do All Traps","Double Take Archery","Doublestar Corp.","DoubleTap Ammunition","DPMS","Drago Gear","DRD Tactical","Drymate","Dura Sight","DURAMAG","E.M.F","EAA","EAA - European American Armory Corp","EAA Corp","EAA|Girsan","Eagle Industries","Easton","Ed Brown","Ed Brown Products","Edgar Sherman Design","Element Outdoors","Eley","Elftmann Tactical","Elite Tactical Systems Group","EMF Inc. DBA Pietta","Energizer","EOTech","Ergo Grip","ET Arms","European Am. Arms","European American Armory","European American Armory|Girsan","Eva-Dry","Evolution Gun Works","Evolution Outdoor","Excel Arms","Exothermic Technologies","F-1 Firearms","F.A.B. Defense","Fail Zero","FailZero","Faxon Firearms","Federal","FightLite","FIME","Fime Group","Fiocchi","Fiocchi Ammunition","Firearm Safety Devices Corporation","Firefield","Firestorm","FK BRNO","Flextone Calls","Flextone Decoys","FLIR","Flitz International","Flying Arrow Archery","FMK Firearms","FMK Firearms Inc.","FN","FN America","FN America Law Enf","Fobus","Fortis Manufacturing, Inc.","Fortune Products (Accusharp)","Fostech","Four Peaks","Frankford Arsenal","Franklin Armory","Freedom Ordance","Freedom Ordnance","Frogg Toggs","FrogLube","FRONTIER","Frontier Cartridge","FSDC","Full Forge Gear","Fusion Firearms","G2 Research","G5 OUTDOORS","G96 Products","Galco","Gamo","Garaysar","Gatco","Geissele","Geissele Automatics","Gemtech","German Precision Optics","German Sport","GForce Arms","GG&G, Inc.","Ghost Inc","Ghost Inc.","Girsan","Glaser","Glaser Ammunition","GlenDel","Glock","Glock Inc.","God'a Grip","Gold Tip","Gorilla Ammunition Company LLC","GPS","Grace USA","Great Lakes Firearms & Ammo","Great Lakes Firearms & Ammunition","Grey Ghost Gear","Grey Ghost Precision","Griffin Armament","Grim Reaper","Grip Pod","GrovTec","Guard dog security","Gun Storage Solutions","GunMate","Guntec USA","GunVault","H&N Sport Pellets","HABIT","Halo Optics","Hammerli Arms","Hamskea Archery","Harris","Harris Engineering","Harvester","Hatsan Airguns","Hatsan USA / Escort Shotguns","Havalon","Havalon Knives","Hawk","Haydel's","Heckler & Koch","Heckler and Koch (HK USA)","Heizer Defense","Henry","Henry Repeating Arms","Henry Repeating Arms Company","Hera USA","Heritage","Heritage Manufacturing","Heritage Manufacturing Inc","HEVI-Shot","HEXMAG","HHA Sports","Hi-Point","Hi-Point Firearms","Hi-Viz","High Mountain Defense Inc dba - Old West Firearms","High Speed Gear","Hiperfire","Hiviz","HK","HKS","HME Products","Hodgdon","Hogue","Holosun","Holosun Technologies","Honeywell Safety Products","Hook's Custom Calls","Hooyman","Hoppe's","Hoppes","Hornady","Hot Shot","Hot Shot Archery","HotHands","Howa","Howard Leight","HSM Ammo","Hunter Safety System","Hunter's Kloak","Hunters Specialties","Hurricane","Huskemaw Optics","HUXWRX Safety Company","Impact Weapons Components","Impala Plus","Industrial Revolution","INFORCE","Inland","Inland Manufacturing","Insights Hunting","International Firearm Corporation","IQ Bowsight","IRAYUSA","IRON CITY RIFLE WORKS","Israel Weapon Industries","ISSC","Istanbul Silah","Italian Firearms Group","Iver Johnson","IWI","IWI - Israel Weapon Industries","IWI US (Israel Weapon Industries)","IWI US, Inc","IWI-US","J&E Machine Tech","Jacob Grey Custom","Johnny Stewart","JTS Group","Just Right Carbine","Just Right Carbines","Ka-Bar Knives","KABAR","Kahr","Kahr Arms","Kahr Arms|Auto-Ordnance","Kahr Arms|Thompson","Kalashnikov USA","KCI USA","KCI USA Inc","KE Arms","Kel-Tec","Kel-Tec Cnc Industries Inc.","Keltec","Keng's","Kershaw","Kestrel","Kestrel Ballistics","Keystone Sporting Arms","Kick's Industries","Kimber","Kinetic Development Group, LLC","Kleen-Bore","Knights Armament Company","KNS Precision","KNS Precision, Inc.","Konus","Koola Buck","Kriss","Kriss USA","KRISS USA, Inc","Kynshot","L.A.G. Tactical, Inc.","Lancer","Landor Arms","Lansky Sharpeners","LanTac USA LLC","Lapua","Laserlyte","LaserMax","LBE Unlimited","Leapers, Inc. - UTG","Lee","Lee Armory","Legacy Sports International","Legacy Sports/Citadel","Leupold","Liberty","Lightfield","LIMBSAVER","Live Free Armory","LKCI","Lockdown","Lone Wolf Distributors","Longshot Target Camera","Lucas Oil","Lucid Optics","Lumenok","Luth-AR","LWRC","Lwrc International","Lyman","M-PRO 7","M+M Industries","Mace","Mace Security International","Mag Storage Solutions","Maglula","Maglula ltd.","MagnetoSpeed","Magnum Research","Magnus Broadheads","MagPod","Magpul","Magpul Industries","Magpump","Magtech","Manticore Arms","Manticore Arms, Inc.","Mantis","Marlin","Master Cutlery","MasterLock","MasterPiece Arms","Mauser","Maverick","Maverick Arms","Max-Ops","Maxim Defense","Maxim Defense Industries","Maxpedition","MCCOY","McCoy Shotguns","MDT","MEC","Mec-Gar Magazines","Mecgar","Mechanix Wear","Meprolight","Mesa Tactical","Metalform Magazines","Metro Arms|American Classic","Michaels","Midwest Industries","Military Armament Corp","Military Arms Corporation","Millennium Marine","Millennium Treestands","Mission Archery","Mission First Tactical","MKS SUPPLY LLC","Mobile Warming","Modern Muzzleloader","Moisture King","MOJO OUTDOORS","Montana Decoy Company","Morakniv","Morrell Targets","Mossberg","Mossberg & Sons Inc.","Mossberg|Mossberg International","Moultrie","Mountain Mike's","Mountain Tactical","MPA","Mr.Heater","MTM","Muddy","Muzzy","NCSTAR","Nemo Arms","New Archery Products","Nexbelt","Night Fision","Nightstick","Nine Line Apparel","Nobelsport","Nockturnal","Nomad","Nordic Components","North American Arms","North American Rescue","Northeast Products","Nose Jammer","NOSLER","Nosler Bullets","Noveske","NXT Generation Toys","Odin Works","Old Timer","Old West","OPSOL","Otis","Otis Technology","Otto","Outdoor Edge","Outers","Pachmayr","Para Ordnance","Pathfinder","Patriot Ordnance Factory","Pearce Grip","Pearce Grips Inc.","Peet Dryer","Pelican","PepperBall","Peregrine Outdoors","Phase 5","Phase 5 Weapon Systems","Phoenix","Phoenix Arms","Pietta","Pine Ridge Archery","Pioneer Arms","Pioneer Arms|Radom","Pitbull Tactical","Pittman Game Calls","Plano","PMC","POF USA","POF-USA","Pointer","Polymer80","PowerBelt Bullets","Predator Tactics","Primary Weapons Systems","Primos","Pro Ears","Pro Mag","Pro-Shot Products","ProMag","Proof Research Inc","Prvi Partizan","PS Products","PSE Archery","PSP Products","PTR Industries","Pulsar","Pulsar Thermal","Puma","Q","Q LLC","QAD","Quake","Quaker Boy","Radian Weapons","Radians","Radical Firearms","RAGE","Ramcat","Ranch Products","Rapid Force","Rapid Rope","Raven Concealment Systems","Ravenwood International","Ravin Crossbows","Rayovac","RCBS","Real Avid","Realtree","Reapr","Red Rock Gear","Redball Sports","REMARMS","Remington","Remington Cutlery","Remington Firearms","Reptilia","Retay USA","Rhino","Rifle Basix","RIG","Riley Defense","Rise Armament","Riton","Riton Optics","Rival Arms","Rivers Edge","Rizzini","Rock Isand Armory","Rock Island Armory","Rock River Arms","Rocket Broadheads","Rocky Mountain","Rocky Mountain Hunting Calls","Rosco Manufacturing","Rossi","RS Regulate","Ruger","Rugged Suppressors","RW Minis","RWC Group LLC - dba Kalashinkov USA","RWS","Sabre","Safariland","Sako","Samson Manufacturing Corp.","San Tan Tactical","SAR USA","SAR USA by Sarsilmaz","Sauer","Savage","Savage Arms","Savage Arms|Stevens","SB Tactical","SCCY","SCCY Firearms","Sccy Industries","Scent Thief","Scentcrusher","Scentlok","Schrade","Scopecoat","Scott Archery","Scout","SDS Imports","Seekins Precision","Sellier & Bellot","SENCUT","SGM Tactical","Shadow Systems","Shadow Systems dba Shadow Gunworks","Shark Coast Tactical","Sharps Bros.","Sheffield","Shield Arms","Shield Sights","Shooter's Choice","Shooters Choice","Shooters Ridge","Shooting Made Easy","Sierra Bullets","SIG","Sig Sauer","Sightmark","Sightron","SilencerCo","Silver Shadow","Simmons","Sionyx","SKB Sports","Skull Hooker","Slick Trick","Slip 2000","Slogan Outdoors","SME Products","Smith & Wesson","Smith & Wesson Inc.","Smith & Wesson Law Enf","Smith & Wesson|Smith & Wesson Performance Ctr","Smith and Wesson","Smith Wesson Dba Thompson Cent","SnapSafe","SOG","SOG Knives & Tools","Sons of Liberty Gun Works","Southern Bloomer","Southern Bloomers","Spartan Camera","Speer","Speer Ammunition","Spike's Tactical","SportDog","Spot Hogg","Springfield","Springfield Armory","Spyderco","Spypoint","SRM Arms","SRM ARMS INC.","Stack-On","Stag Arms","Stag Arms LLC","Standard Manufacturing","Standard Manufacturing Company","Standard Mfg Co","Standard MFG Co LLC","Standard MFG.","Stars and Stripes Defense Ammunition","Stealth Cam","Stealth Operator","Stealth Operator Holster","Steiner","Stern Defense","Stevens","Steyr","Steyr Arms","Sticky Holsters","STREAK Ammunition","Streamlight","Strike Industries","Striker","Strongsuit","Sturm Ruger & Co","STV Technology","Summit","Surefire","Surefire Llc","Surgeon","Swab-Its","Swagger","Swhacker","Sylvan Arms","SZCO Rite Edge","SZCO Sawmill","SZCO Sierra Zulu","SZCO Steel Stag","T.R.U. Ball Archery","Tac Shield","TAC VANES","Tacfire Inc.","TacStar","Tactacam","Tactical Innovations","Tactical Solutions","Tactical Superiority","Tagua","Talley Manufacturing","TALON Grips Inc","Tanfoglio","TangoDown","Tannerite","Target Sports","Tasco","Taser","Taurus","Taurus International - Heritage","Taurus International Inc.","Taurus InternationalInc - Rossi","Techna Clip","Techna Clips","TekMat","Templar Knife","TEN POINT","Tenzing","The Outdoor Connection","Thermacell","Thompson","Thompson Center","Thorn Broadheads","Thril","Tight Spot","Tikka","Timney","Timney Triggers","Tinks","Tippmann Arms Company","Tipton","Tisas","Titan 3D","Tnw Firearms","Tokarev Shotguns","Tokarev USA","Top Brass","Tornado Personal Defense","Torrent Suppressors","TPS Arms","TR Imports","Traditions","Traditions Performance Firearms","Trailblazer","Trailblazer Firearms","TriggerTech","Trijicon","Trijicon Electro Optics","TriStar","TriStar Arms Inc.","TriStar Sporting Arms","Trius","Trophy Ridge","Trophy Taker","Troy","Troy Industries","True Precision","Trufire","Truglo","Truglo Bowfishing","Two Two Three Innovations","Ulfhednar","Ultimate Survival Technologies","Ultradot","Ultradyne","Ultradyne USA","Umarex","Umarex USA","Unbranded AR","UNBRANDEDAR","Uncle Henry","Uncle Mike's","Underwood Ammo","Unity Tactical","US Optics","US Palm","US PeaceKeeper","Used Glock|Glock","UST - Ultimate Survival Technologies","UTAS","VaporTrail","VersaCarry","Vertx","Victory Archery","Viper Archery Products","Viridian","Viridian Weapon Technologies","Volquartsen Firearms","Walker's","Walkers","Walther","Walther Arms","Walther Arms Inc","Walther Arms Inc|Colt","Warne","Warne Scope Mounts","Wasp Archery Products","WE Knife","Weatherby","Weaver","Western Rivers","Wheeler","Whitetail Institute","Wicked Ridge","Wicked Tree Gear","Wildlife Research","Williams Gunsight Co.","Wilson Combat","Winchester","Winchester Ammunition","Winchester Repeating Arms","Windham Weaponry","WMD Guns","Woodhaven Calls","Woox","Wraithworks","X-GRIP","Xpedition Crossbows","XS Sight Systems","XS Sights","Yankee Hill Machine","Yankee Hill Machine Co","Yankee Hill Machine Company","Zac Brown's Southern Grind","Zaffiri Precision","Zastava","Zastava Arms USA","Zenith Firearms","ZEV Technologies","ZRO DELTA","ZRO Delta LLC"]
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
                                                        "Chatanooga Shooting Supplies": {
                                                            "description": "Chatanooga Shooting Supplies Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_cssi"
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
                                                            }
                                                        },
                                                        "upc": {
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
                                                            }
                                                        },
                                                        "brand": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string",
                                                                        "enum": all_brands
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string",
                                                                        "enum": all_brands
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        "category": {
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
                                                            }
                                                        },
                                                        "product_class": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string",
                                                                        "enum": ["AC","AG","AO","AP","AR","BP","FA","FI","FP","HT","HZ","KN","MG","MZ","OP","OT","RL","SO"]
                                                                        /*
                                                                        "oneOf": [
                                                                            {const: "AC", title: "Accessories"},
                                                                            {const: "AG", title: "Air Guns & Accessories"},
                                                                            {const: "AO", title: "Ammunition"},
                                                                            {const: "AP", title: "Apparel"},
                                                                            {const: "AR", title: "Archery"},
                                                                            {const: "BP", title: "Black Powder Firearms"},
                                                                            {const: "FA", title: "Firearms"},
                                                                            {const: "FI", title: "Fishing"},
                                                                            {const: "FP", title: "Firearms Parts"},
                                                                            {const: "HT", title: "Hunting"},
                                                                            {const: "HZ", title: "Hazardous"},
                                                                            {const: "KN", title: "Knives"},
                                                                            {const: "MG", title: "Magazines"},
                                                                            {const: "MZ", title: "Muzzleloading"},
                                                                            {const: "OP", title: "Optics"},
                                                                            {const: "OT", title: "Other"},
                                                                            {const: "RL", title: "Reloading"},
                                                                            {const: "SO", title: "SOT"}
                                                                        ]
                                                                        */
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string",
                                                                        "enum": ["AC","AG","AO","AP","AR","BP","FA","FI","FP","HT","HZ","KN","MG","MZ","OP","OT","RL","SO"]
                                                                        /*
                                                                        "oneOf": [
                                                                            {const: "AC", title: "Accessories"},
                                                                            {const: "AG", title: "Air Guns & Accessories"},
                                                                            {const: "AO", title: "Ammunition"},
                                                                            {const: "AP", title: "Apparel"},
                                                                            {const: "AR", title: "Archery"},
                                                                            {const: "BP", title: "Black Powder Firearms"},
                                                                            {const: "FA", title: "Firearms"},
                                                                            {const: "FI", title: "Fishing"},
                                                                            {const: "FP", title: "Firearms Parts"},
                                                                            {const: "HT", title: "Hunting"},
                                                                            {const: "HZ", title: "Hazardous"},
                                                                            {const: "KN", title: "Knives"},
                                                                            {const: "MG", title: "Magazines"},
                                                                            {const: "MZ", title: "Muzzleloading"},
                                                                            {const: "OP", title: "Optics"},
                                                                            {const: "OT", title: "Other"},
                                                                            {const: "RL", title: "Reloading"},
                                                                            {const: "SO", title: "SOT"}
                                                                        ]
                                                                        */
                                                                    }
                                                                }
                                                            }
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
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "category": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "product_class": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
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
                                                        "url": {
                                                            "type": "string",
                                                            "format": "uri",
                                                            "qt-uri-protocols": [
                                                                "https"
                                                            ]
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
                                                        "key",
                                                        "listing_duration",
                                                        "password",
                                                        "payment_methods",
                                                        "pricing",
                                                        "standard_description_html",
                                                        "standard_text_id",
                                                        "url",
                                                        "username"
                                                    ]
                                                },
                                                "distributor_cssi": {
                                                    "title": "Chatanooga Shooting Supplies",
                                                    "description": "Chatanooga Shooting Supplies Product Feed Configuration",
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
                <div class="postbox" style="padding: 10px;margin-top: 10px">

                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="order_fulfillment_table"></div>
                    <div style="padding:5px;"><button id="download_fulfillment_history_button" class="button alt" data-marker-id="">Download Fulfillment History</button></div>
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
                                {name: 'Dist. Order', width: '75px',
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
                                {name: 'Phone', width: '120px'}, 
                                {name: 'Email', width: '100px',
                                    formatter: (_, row) => gridjs.html(`<a href="mailto:${row.cells[7].data}">Email</a>`)
                                },
                                {name: 'Ordered', width: '125px',
                                    formatter: (cell) => `${cell.substring(0,10)}`
                                },
                                {name: 'Shipped', width: '100px',
                                    formatter: (cell) => `${cell!=null?cell.substring(0,10):""}`
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

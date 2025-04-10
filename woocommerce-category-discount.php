                                    "><?php _e('Boş bırakılırsa, süresiz olarak uygulanacaktır.', 'woocommerce-reduction-categorie'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Aktif Et', 'woocommerce-reduction-categorie'); ?></th>
                                <td>
                                    <input type="checkbox" name="discount_enabled" value="1" <?php checked($edit_discount ? $edit_discount['enabled'] : 1, 1); ?> />
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button($edit_discount ? __('İndirimi Güncelle', 'woocommerce-reduction-categorie') : __('Yeni İndirim Ekle', 'woocommerce-reduction-categorie')); ?>
                    </form>
                    
                    <h2><?php _e('Mevcut İndirimler', 'woocommerce-reduction-categorie'); ?></h2>
                    
                    <?php if (empty($discounts)): ?>
                        <p><?php _e('Henüz indirim eklenmemiş.', 'woocommerce-reduction-categorie'); ?></p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('İndirim Adı', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Tür', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Kategori/Marka', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Oran', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Rozet Metni', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Son Tarih', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('Durum', 'woocommerce-reduction-categorie'); ?></th>
                                    <th><?php _e('İşlemler', 'woocommerce-reduction-categorie'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($discounts as $discount_id => $discount): ?>
                                    <?php 
                                    // Kategori veya marka adını al
                                    $term_name = '';
                                    if ($discount['type'] === 'category') {
                                        $term = get_term($discount['term_id'], 'product_cat');
                                        if (!is_wp_error($term) && $term) {
                                            $term_name = $term->name;
                                        }
                                    } else {
                                        // Marka taksonomisini bul
                                        $possible_taxonomies = array('product_brand', 'pa_brand', 'brand', 'pwb-brand', 'yith_product_brand');
                                        foreach ($possible_taxonomies as $tax) {
                                            if (taxonomy_exists($tax)) {
                                                $term = get_term($discount['term_id'], $tax);
                                                if (!is_wp_error($term) && $term) {
                                                    $term_name = $term->name;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // İndirimin aktif olup olmadığını kontrol et
                                    $is_active = $discount['enabled'] ? true : false;
                                    
                                    // Son kullanma tarihi geçmiş mi kontrol et
                                    if (!empty($discount['end_date'])) {
                                        $end_date = strtotime($discount['end_date']);
                                        $today = strtotime('today');
                                        if ($end_date < $today) {
                                            $is_active = false;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($discount['name']); ?></td>
                                        <td><?php echo $discount['type'] === 'category' ? __('Kategori', 'woocommerce-reduction-categorie') : __('Marka', 'woocommerce-reduction-categorie'); ?></td>
                                        <td><?php echo esc_html($term_name); ?></td>
                                        <td><?php echo esc_html($discount['amount']); ?>%</td>
                                        <td><?php echo esc_html($discount['badge_text']); ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($discount['end_date'])) {
                                                echo date_i18n(get_option('date_format'), strtotime($discount['end_date']));
                                            } else {
                                                echo __('Süresiz', 'woocommerce-reduction-categorie');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($is_active): ?>
                                                <span class="status-active"><?php _e('Aktif', 'woocommerce-reduction-categorie'); ?></span>
                                            <?php else: ?>
                                                <span class="status-inactive"><?php _e('Pasif', 'woocommerce-reduction-categorie'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('admin.php?page=wc-kategori-indirim&action=edit&discount_id=' . urlencode($discount_id)); ?>" class="button button-small"><?php _e('Düzenle', 'woocommerce-reduction-categorie'); ?></a>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=wc-kategori-indirim&action=delete&discount_id=' . urlencode($discount_id)), 'delete_discount'); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Bu indirimi silmek istediğinizden emin misiniz?', 'woocommerce-reduction-categorie'); ?>')"><?php _e('Sil', 'woocommerce-reduction-categorie'); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <div class="apply-discounts-container" style="margin-top: 20px;">
                        <h3><?php _e('İndirimleri Şimdi Uygula', 'woocommerce-reduction-categorie'); ?></h3>
                        <p><?php _e('İndirimler otomatik olarak uygulanmadıysa, aşağıdaki butona tıklayarak manuel olarak uygulayabilirsiniz.', 'woocommerce-reduction-categorie'); ?></p>
                        <form method="post" action="">
                            <?php wp_nonce_field('apply_discounts_manually', 'apply_discounts_nonce'); ?>
                            <input type="hidden" name="apply_discounts_manually" value="1">
                            <input type="submit" class="button button-primary" value="<?php _e('İndirimleri Şimdi Uygula', 'woocommerce-reduction-categorie'); ?>">
                        </form>
                    </div>
                </div>
                
                <!-- Ayarlar sekmesi -->
                <div id="settings" class="tab-content" style="display: none;">
                    <h2><?php _e('Genel Ayarlar', 'woocommerce-reduction-categorie'); ?></h2>
                    
                    <form method="post" action="options.php">
                        <?php settings_fields('wc_kategori_indirim_settings'); ?>
                        <?php do_settings_sections('wc_kategori_indirim_settings'); ?>
                        
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Rozet Rengi', 'woocommerce-reduction-categorie'); ?></th>
                                <td>
                                    <input type="color" name="wc_kategori_indirim_badge_color" value="<?php echo esc_attr(get_option('wc_kategori_indirim_badge_color', '#dd3333')); ?>" />
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button(); ?>
                    </form>
                </div>
                
                <!-- Yardım sekmesi -->
                <div id="help" class="tab-content" style="display: none;">
                    <h2><?php _e('Nasıl Çalışır?', 'woocommerce-reduction-categorie'); ?></h2>
                    <ol>
                        <li><?php _e('Yeni bir indirim eklemek için "İndirimler" sekmesinden "İndirim Adı" ve diğer bilgileri girin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('İndirim türünü (kategori veya marka) seçin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('Seçiminize göre kategori veya marka belirleyin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('İndirim oranını belirleyin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('Ürünlerin üzerinde görünecek olan rozet metnini girin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('İsterseniz indirimin geçerlilik süresini belirleyin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('"Yeni İndirim Ekle" butonuna tıklayarak indirimi kaydedin.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('İndirimler otomatik olarak uygulanacaktır. Ancak herhangi bir sorun yaşarsanız, "İndirimleri Şimdi Uygula" butonunu kullanabilirsiniz.', 'woocommerce-reduction-categorie'); ?></li>
                    </ol>
                    
                    <h3><?php _e('Not:', 'woocommerce-reduction-categorie'); ?></h3>
                    <ul>
                        <li><?php _e('Bu eklenti, ürünlerin gerçek satış fiyatlarını değiştirir, bu nedenle sepet ve ödeme sayfalarında da indirimli fiyatlar görünür.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('Birden fazla indirim aynı ürüne uygulanabilir durumda ise, en yüksek indirim oranı geçerli olacaktır.', 'woocommerce-reduction-categorie'); ?></li>
                        <li><?php _e('Son kullanma tarihi geçen indirimler otomatik olarak devre dışı bırakılır.', 'woocommerce-reduction-categorie'); ?></li>
                    </ul>
                </div>
            </div>
            
            <!-- Sekme navigasyonu için JavaScript -->
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Sekme navigasyonu
                $('.nav-tab-wrapper a').on('click', function(e) {
                    e.preventDefault();
                    
                    // Aktif sekmeyi değiştir
                    $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');
                    
                    // İlgili içeriği göster/gizle
                    $('.tab-content').hide();
                    $($(this).attr('href')).show();
                });
                
                // İndirim türüne göre alan gösterme/gizleme
                function toggleDiscountTypeFields() {
                    var selectedType = $('#discount-type').val();
                    
                    if (selectedType === 'category') {
                        $('#discount-category-row').show();
                        $('#discount-category-select').prop('disabled', false);
                        $('#discount-brand-row').hide();
                        $('#discount-brand-select').prop('disabled', true);
                    } else {
                        $('#discount-category-row').hide();
                        $('#discount-category-select').prop('disabled', true);
                        $('#discount-brand-row').show();
                        $('#discount-brand-select').prop('disabled', false);
                    }
                }
                
                // İlk yüklemede uygula
                toggleDiscountTypeFields();
                
                // Seçim değiştiğinde uygula
                $('#discount-type').on('change', toggleDiscountTypeFields);
            });
            </script>
            
            <!-- CSS Stilleri -->
            <style>
            .tab-content {
                margin-top: 20px;
            }
            
            .status-active {
                color: #46b450;
                font-weight: bold;
            }
            
            .status-inactive {
                color: #dc3232;
            }
            
            .wc-kategori-indirim-info {
                margin-top: 20px;
                padding: 15px;
                background-color: #f8f8f8;
                border-radius: 5px;
            }
            </style>
        </div>
        <?php
    }

    // Süresi geçmiş indirimleri kontrol et
    public function check_expired_discounts() {
        $discounts = get_option('wc_kategori_indirim_discounts', array());
        $updated = false;
        
        foreach ($discounts as $id => $discount) {
            // Eğer son kullanma tarihi varsa ve geçmişse
            if (!empty($discount['end_date'])) {
                $end_date = strtotime($discount['end_date']);
                $today = strtotime('today');
                
                if ($end_date < $today && $discount['enabled']) {
                    // İndirimi devre dışı bırak
                    $discounts[$id]['enabled'] = 0;
                    $updated = true;
                }
            }
        }
        
        if ($updated) {
            update_option('wc_kategori_indirim_discounts', $discounts);
            $this->apply_discounts_to_products();
        }
    }<?php
/**
 * Plugin Name: WooCommerce Kategori ve Marka İndirimi
 * Plugin URI: http://yourwebsite.com
 * Description: Belirli kategorilere veya markalara toplu indirim uygulayın ve son kullanma tarihi belirleyin.
 * Version: 2.0
 * Author: Your Name
 * Author URI: http://yourwebsite.com
 * Text Domain: woocommerce-reduction-categorie
 * Domain Path: /languages
 * 
 * WC requires at least: 3.0.0
 * WC tested up to: 8.5.0
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Vérifier si WooCommerce est actif
function wckategori_indirim_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'wckategori_indirim_woocommerce_missing_notice');
        return false;
    }
    return true;
}

// Afficher un avertissement si WooCommerce n'est pas installé
function wckategori_indirim_woocommerce_missing_notice() {
    echo '<div class="error"><p>' . sprintf(__('L\'extension WooCommerce Réduction par Catégorie nécessite que %sWooCommerce%s soit installé et activé.', 'woocommerce-reduction-categorie'), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a>') . '</p></div>';
}

// Eklentinin ana sınıfı
class WC_Kategori_Indirim {
    
    // Sınıf örneği
    protected static $instance = null;
    
    // Sınıf örneğini alma
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Yapıcı metod
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_apply_discounts_manually'));
        
        // İndirimlerin uygulanması için kancalar
        add_action('woocommerce_loaded', array($this, 'apply_discounts_to_products'));
        
        // İndirim rozetlerini göstermek için kancalar
        add_action('wp_head', array($this, 'add_discount_badge_css'));
        add_action('woocommerce_before_shop_loop_item_title', array($this, 'add_discount_badge'), 10);
        
        // Ürün ve kategori sayfalarında indirim bilgilerini gösterme
        add_action('woocommerce_single_product_summary', array($this, 'add_discount_info_on_single'), 11);
        add_action('woocommerce_archive_description', array($this, 'add_discount_info_on_category'), 5);
        
        // Bilgi meta kutusu
        add_action('add_meta_boxes', array($this, 'add_discount_info_box'));
        
        // Zamanlanmış görevler için kanca
        add_action('wc_kategori_indirim_check_expired', array($this, 'check_expired_discounts'));
        
        // Zamanlanmış görevi etkinleştir
        if (!wp_next_scheduled('wc_kategori_indirim_check_expired')) {
            wp_schedule_event(time(), 'hourly', 'wc_kategori_indirim_check_expired');
        }
    }
    
    // İndirimlerin manuel olarak uygulanmasını işle
    public function handle_apply_discounts_manually() {
        if (isset($_POST['apply_discounts_manually']) && $_POST['apply_discounts_manually'] == '1') {
            // Güvenlik için nonce kontrolü
            if (!isset($_POST['apply_discounts_nonce']) || !wp_verify_nonce($_POST['apply_discounts_nonce'], 'apply_discounts_manually')) {
                wp_die(__('Güvenlik hatası: Bu işlemi gerçekleştirme izniniz yok.', 'woocommerce-reduction-categorie'));
            }
            
            // İndirimleri şimdi uygula
            $this->apply_discounts_to_products();
            
            // Başarı mesajı ekle
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('İndirimler başarıyla uygulandı!', 'woocommerce-reduction-categorie') . '</p></div>';
            });
        }
    }
    
    // Yönetim menüsüne ekle
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('Kategori ve Marka İndirimleri', 'woocommerce-reduction-categorie'),
            __('Kategori ve Marka İndirimleri', 'woocommerce-reduction-categorie'),
            'manage_woocommerce',
            'wc-kategori-indirim',
            array($this, 'admin_page')
        );
    }
    
    // Ayarları kaydetme
    public function register_settings() {
        register_setting('wc_kategori_indirim_settings', 'wc_kategori_indirim_discounts', array($this, 'settings_updated'));
        register_setting('wc_kategori_indirim_settings', 'wc_kategori_indirim_badge_color', array($this, 'settings_updated'));
    }
    
    // Ayarlar güncellendiğinde çalışacak fonksiyon
    public function settings_updated($value) {
        // Ayarlar güncellendiğinde indirimleri uygula
        add_action('shutdown', array($this, 'apply_discounts_to_products'));
        return $value;
    }
    
    // Afficher la page d'administration
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('WooCommerce Réduction par Catégorie', 'woocommerce-reduction-categorie'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('wc_kategori_indirim_settings'); ?>
                <?php do_settings_sections('wc_kategori_indirim_settings'); ?>
                
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Activer la réduction', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <input type="checkbox" name="wc_kategori_indirim_enabled" value="1" <?php checked(1, get_option('wc_kategori_indirim_enabled'), true); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Type de réduction', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <select name="wc_kategori_indirim_type" id="wc-kategori-indirim-type">
                                <option value="category" <?php selected(get_option('wc_kategori_indirim_type', 'category'), 'category'); ?>><?php _e('Par catégorie', 'woocommerce-reduction-categorie'); ?></option>
                                <option value="brand" <?php selected(get_option('wc_kategori_indirim_type', 'category'), 'brand'); ?>><?php _e('Par marque', 'woocommerce-reduction-categorie'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top" id="wc-kategori-indirim-category-row">
                        <th scope="row"><?php _e('Sélectionner une catégorie', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <select name="wc_kategori_indirim_category">
                                <option value=""><?php _e('Choisir une catégorie', 'woocommerce-reduction-categorie'); ?></option>
                                <?php
                                $product_categories = get_terms('product_cat', array('hide_empty' => false));
                                $selected_category = get_option('wc_kategori_indirim_category');
                                
                                foreach ($product_categories as $category) {
                                    echo '<option value="' . esc_attr($category->term_id) . '" ' . selected($selected_category, $category->term_id, false) . '>' . esc_html($category->name) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top" id="wc-kategori-indirim-brand-row">
                        <th scope="row"><?php _e('Sélectionner une marque', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <select name="wc_kategori_indirim_brand">
                                <option value=""><?php _e('Choisir une marque', 'woocommerce-reduction-categorie'); ?></option>
                                <?php
                                // Vérifier si la taxonomie 'product_brand' existe (utilisée par de nombreux plugins de marques)
                                $brand_taxonomy = 'product_brand';
                                if (taxonomy_exists($brand_taxonomy)) {
                                    $brands = get_terms($brand_taxonomy, array('hide_empty' => false));
                                } else {
                                    // Essayer d'autres taxonomies couramment utilisées pour les marques
                                    $possible_taxonomies = array('pa_brand', 'brand', 'pwb-brand', 'yith_product_brand');
                                    $brands = array();
                                    
                                    foreach ($possible_taxonomies as $tax) {
                                        if (taxonomy_exists($tax)) {
                                            $brand_taxonomy = $tax;
                                            $brands = get_terms($tax, array('hide_empty' => false));
                                            break;
                                        }
                                    }
                                }
                                
                                $selected_brand = get_option('wc_kategori_indirim_brand');
                                
                                if (!empty($brands)) {
                                    foreach ($brands as $brand) {
                                        echo '<option value="' . esc_attr($brand->term_id) . '" ' . selected($selected_brand, $brand->term_id, false) . '>' . esc_html($brand->name) . '</option>';
                                    }
                                } else {
                                    echo '<option value="" disabled>' . __('Aucune marque trouvée. Installez un plugin de marques.', 'woocommerce-reduction-categorie') . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Taux de réduction (%)', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <input type="number" name="wc_kategori_indirim_amount" value="<?php echo esc_attr(get_option('wc_kategori_indirim_amount', 20)); ?>" min="0" max="100" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Texte du badge de réduction', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <input type="text" name="wc_kategori_indirim_badge_text" value="<?php echo esc_attr(get_option('wc_kategori_indirim_badge_text', '-20%')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Couleur du badge', 'woocommerce-reduction-categorie'); ?></th>
                        <td>
                            <input type="color" name="wc_kategori_indirim_badge_color" value="<?php echo esc_attr(get_option('wc_kategori_indirim_badge_color', '#dd3333')); ?>" />
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <?php if (get_option('wc_kategori_indirim_enabled') && (get_option('wc_kategori_indirim_category') || get_option('wc_kategori_indirim_brand'))) : ?>
            <div class="apply-discounts-container" style="margin-top: 20px;">
                <h3><?php _e('Appliquer les réductions maintenant', 'woocommerce-reduction-categorie'); ?></h3>
                <p><?php _e('Si les réductions ne sont pas appliquées automatiquement, cliquez sur le bouton ci-dessous pour les appliquer manuellement.', 'woocommerce-reduction-categorie'); ?></p>
                <form method="post" action="">
                    <?php wp_nonce_field('apply_discounts_manually', 'apply_discounts_nonce'); ?>
                    <input type="hidden" name="apply_discounts_manually" value="1">
                    <input type="submit" class="button button-primary" value="<?php _e('Appliquer les réductions maintenant', 'woocommerce-reduction-categorie'); ?>">
                </form>
            </div>
            <?php endif; ?>
            
            <div class="wc-kategori-indirim-info">
                <h2><?php _e('Comment ça marche ?', 'woocommerce-reduction-categorie'); ?></h2>
                <ol>
                    <li><?php _e('Sélectionnez le type de réduction (catégorie ou marque).', 'woocommerce-reduction-categorie'); ?></li>
                    <li><?php _e('Choisissez la catégorie ou la marque concernée.', 'woocommerce-reduction-categorie'); ?></li>
                    <li><?php _e('Définissez le taux de réduction.', 'woocommerce-reduction-categorie'); ?></li>
                    <li><?php _e('Personnalisez l\'apparence du badge de réduction.', 'woocommerce-reduction-categorie'); ?></li>
                    <li><?php _e('Enregistrez les paramètres.', 'woocommerce-reduction-categorie'); ?></li>
                    <li><?php _e('La réduction sera automatiquement appliquée à tous les produits concernés.', 'woocommerce-reduction-categorie'); ?></li>
                </ol>
                
                <p><strong><?php _e('Note :', 'woocommerce-reduction-categorie'); ?></strong> <?php _e('Cette réduction modifie réellement le prix affiché et sera visible dans le panier et à la caisse.', 'woocommerce-reduction-categorie'); ?></p>
            </div>
            
            <!-- Script pour afficher/masquer les champs selon le type de réduction -->
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Fonction pour afficher le bon champ selon le type de réduction
                function toggleDiscountTypeFields() {
                    var selectedType = $('#wc-kategori-indirim-type').val();
                    
                    if (selectedType === 'category') {
                        $('#wc-kategori-indirim-category-row').show();
                        $('#wc-kategori-indirim-brand-row').hide();
                    } else {
                        $('#wc-kategori-indirim-category-row').hide();
                        $('#wc-kategori-indirim-brand-row').show();
                    }
                }
                
                // Appliquer au chargement
                toggleDiscountTypeFields();
                
                // Appliquer au changement
                $('#wc-kategori-indirim-type').on('change', toggleDiscountTypeFields);
            });
            </script>
        </div>
        <?php
    }

    // Appliquer les remises à tous les produits concernés
    public function apply_discounts_to_products() {
        // Vérifier si la remise est activée
        if (!get_option('wc_kategori_indirim_enabled')) {
            // Si la remise est désactivée, supprimer les prix de vente
            $this->remove_all_discounts();
            return;
        }
        
        // Type de réduction (catégorie ou marque)
        $discount_type = get_option('wc_kategori_indirim_type', 'category');
        
        // ID de la taxonomie sélectionnée
        $term_id = 0;
        $taxonomy = '';
        
        if ($discount_type === 'category') {
            $term_id = get_option('wc_kategori_indirim_category');
            $taxonomy = 'product_cat';
        } else {
            $term_id = get_option('wc_kategori_indirim_brand');
            
            // Déterminer la taxonomie de marque utilisée
            $possible_taxonomies = array('product_brand', 'pa_brand', 'brand', 'pwb-brand', 'yith_product_brand');
            foreach ($possible_taxonomies as $tax) {
                if (taxonomy_exists($tax)) {
                    $taxonomy = $tax;
                    break;
                }
            }
        }
        
        // Si aucune catégorie/marque n'est sélectionnée ou si la taxonomie n'existe pas, ne rien faire
        if (empty($term_id) || empty($taxonomy)) {
            return;
        }
        
        // Taux de réduction
        $discount_percentage = floatval(get_option('wc_kategori_indirim_amount', 20));
        
        // Récupérer tous les produits concernés
        $products = $this->get_products_by_term($term_id, $taxonomy);
        
        // Tableau pour suivre les produits traités
        $processed_products = array();
        
        // Appliquer la remise à chaque produit
        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
            
            // Ajouter à la liste des produits traités
            $processed_products[] = $product_id;
            
            // Si c'est un produit variable, traiter chaque variation
            if ($product->is_type('variable')) {
                $variations = $product->get_available_variations();
                foreach ($variations as $variation) {
                    $variation_obj = wc_get_product($variation['variation_id']);
                    $this->apply_discount_to_product($variation_obj, $discount_percentage);
                }
            } else {
                // Pour les produits simples
                $this->apply_discount_to_product($product, $discount_percentage);
            }
        }
        
        // Supprimer les réductions des produits qui ne font pas partie de la catégorie/marque actuelle mais qui avaient des réductions
        $this->clean_other_products_discounts($processed_products);
    }
    
    // Appliquer une remise à un produit spécifique
    private function apply_discount_to_product($product, $discount_percentage) {
        if (!$product) return;
        
        $product_id = $product->get_id();
        
        // Récupérer le prix régulier
        $regular_price = floatval($product->get_regular_price());
        
        if ($regular_price > 0) {
            // Calculer le prix réduit
            $sale_price = $regular_price - ($regular_price * ($discount_percentage / 100));
            
            // Arrondir à deux décimales
            $sale_price = round($sale_price, 2);
            
            // Vérifier si le prix de vente est différent du prix régulier
            if ($sale_price < $regular_price) {
                // Définir le prix de vente
                update_post_meta($product_id, '_sale_price', $sale_price);
                update_post_meta($product_id, '_price', $sale_price);
                
                // Marquer le produit comme ayant une réduction de notre extension
                update_post_meta($product_id, '_wc_kategori_indirim_applied', '1');
            }
        }
    }
    
    // Supprimer les réductions des produits qui ne font pas partie de la catégorie actuelle
    private function clean_other_products_discounts($processed_products) {
        global $wpdb;
        
        // Récupérer tous les produits qui ont notre marque de réduction
        $marked_products = $wpdb->get_col(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_kategori_indirim_applied' AND meta_value = '1'"
        );
        
        // Trouver les produits qui ont été retirés de la catégorie
        $to_remove = array_diff($marked_products, $processed_products);
        
        // Supprimer les réductions de ces produits
        foreach ($to_remove as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
            
            // Supprimer le prix de vente
            delete_post_meta($product_id, '_sale_price');
            
            // Rétablir le prix original
            $regular_price = get_post_meta($product_id, '_regular_price', true);
            if ($regular_price) {
                update_post_meta($product_id, '_price', $regular_price);
            }
            
            // Supprimer notre marque
            delete_post_meta($product_id, '_wc_kategori_indirim_applied');
        }
    }
    
    // Supprimer toutes les réductions appliquées par notre extension
    private function remove_all_discounts() {
        global $wpdb;
        
        // Récupérer tous les produits qui ont notre marque de réduction
        $marked_products = $wpdb->get_col(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_kategori_indirim_applied' AND meta_value = '1'"
        );
        
        // Supprimer les réductions de tous ces produits
        foreach ($marked_products as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
            
            // Supprimer le prix de vente
            delete_post_meta($product_id, '_sale_price');
            
            // Rétablir le prix original
            $regular_price = get_post_meta($product_id, '_regular_price', true);
            if ($regular_price) {
                update_post_meta($product_id, '_price', $regular_price);
            }
            
            // Supprimer notre marque
            delete_post_meta($product_id, '_wc_kategori_indirim_applied');
        }
    }
    
    // Récupérer tous les produits par terme de taxonomie
    private function get_products_by_term($term_id, $taxonomy) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
            ),
        );
        
        return get_posts($args);
    }
    
    // Modifier la requête (peut être utilisé ultérieurement)
    public function modify_query($query) {
        return $query;
    }
    
    // Ajouter le CSS du badge de réduction
    public function add_discount_badge_css() {
        $badge_color = get_option('wc_kategori_indirim_badge_color', '#dd3333');
        ?>
        <style>
            /* Badge de réduction flottant */
            .wc-kategori-indirim-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 20;
                background-color: <?php echo esc_attr($badge_color); ?>;
                color: #ffffff;
                padding: 8px 12px;
                font-size: 14px;
                font-weight: bold;
                border-radius: 50px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                transform: rotate(5deg);
            }
            
            .wc-kategori-indirim-info {
                margin-top: 20px;
                padding: 15px;
                background-color: #f8f8f8;
                border-radius: 5px;
            }
        </style>
        <?php
    }
    
    // Ajouter le badge de réduction
    public function add_discount_badge() {
        global $product;
        
        // Vérifier si la remise est activée
        if (!get_option('wc_kategori_indirim_enabled')) {
            return;
        }
        
        // Type de réduction
        $discount_type = get_option('wc_kategori_indirim_type', 'category');
        $term_id = ($discount_type === 'category') ? get_option('wc_kategori_indirim_category') : get_option('wc_kategori_indirim_brand');
        $taxonomy = ($discount_type === 'category') ? 'product_cat' : $this->get_brand_taxonomy();
        
        // Si aucun terme n'est sélectionné, ne pas afficher le badge
        if (empty($term_id) || empty($taxonomy)) {
            return;
        }
        
        // Vérifier si le produit appartient au terme sélectionné
        if (has_term($term_id, $taxonomy, $product->get_id())) {
            $badge_text = get_option('wc_kategori_indirim_badge_text', '-20%');
            echo '<span class="wc-kategori-indirim-badge">' . esc_html($badge_text) . '</span>';
        }
    }
    
    // Ajouter les informations de réduction sur la page produit (version améliorée)
    public function add_discount_info_on_single() {
        global $product;
        
        // Vérifier si la remise est activée
        if (!get_option('wc_kategori_indirim_enabled')) {
            return;
        }
        
        // Type de réduction
        $discount_type = get_option('wc_kategori_indirim_type', 'category');
        $term_id = ($discount_type === 'category') ? get_option('wc_kategori_indirim_category') : get_option('wc_kategori_indirim_brand');
        $taxonomy = ($discount_type === 'category') ? 'product_cat' : $this->get_brand_taxonomy();
        
        // Si aucun terme n'est sélectionné, ne pas afficher les informations
        if (empty($term_id) || empty($taxonomy)) {
            return;
        }
        
        // Vérifier si le produit appartient au terme sélectionné
        if (has_term($term_id, $taxonomy, $product->get_id())) {
            $discount_percentage = floatval(get_option('wc_kategori_indirim_amount', 20));
            
            // Affichage amélioré de l'information de réduction
            echo '<div class="wc-kategori-indirim-single-info">';
            echo '&#9733; ' . sprintf(__('Profitez de %s%% de réduction sur ce produit !', 'woocommerce-reduction-categorie'), $discount_percentage);
            echo '</div>';
        }
    }
    
    // Ajouter les informations de réduction sur la page de catégorie (version améliorée)
    public function add_discount_info_on_category() {
        // Vérifier si nous sommes sur une page d'archive de produits
        if (!is_product_category() && !is_product_taxonomy()) {
            return;
        }
        
        // Vérifier si la remise est activée
        if (!get_option('wc_kategori_indirim_enabled')) {
            return;
        }
        
        // Type de réduction
        $discount_type = get_option('wc_kategori_indirim_type', 'category');
        $term_id = ($discount_type === 'category') ? get_option('wc_kategori_indirim_category') : get_option('wc_kategori_indirim_brand');
        $taxonomy = ($discount_type === 'category') ? 'product_cat' : $this->get_brand_taxonomy();
        
        // Si aucun terme n'est sélectionné, ne pas afficher les informations
        if (empty($term_id) || empty($taxonomy)) {
            return;
        }
        
        // Obtenir le terme actuel
        $current_term = get_queried_object();
        
        // Vérifier si nous sommes sur la page de catégorie/marque concernée par la remise
        if ($current_term && $current_term->term_id == $term_id && $current_term->taxonomy == $taxonomy) {
            $discount_percentage = floatval(get_option('wc_kategori_indirim_amount', 20));
            $badge_color = get_option('wc_kategori_indirim_badge_color', '#dd3333');
            
            echo '<div class="wc-kategori-indirim-category-info">';
            echo '<strong>' . sprintf(__('&#11088; OFFRE SPÉCIALE : %s%% DE RÉDUCTION', 'woocommerce-reduction-categorie'), 
                    $discount_percentage) . '</strong>';
            echo '<p>' . sprintf(__('Tous les produits de cette %s bénéficient actuellement d\'une réduction exceptionnelle! Profitez-en dès maintenant.', 'woocommerce-reduction-categorie'),
                    ($discount_type === 'category') ? __('catégorie', 'woocommerce-reduction-categorie') : __('marque', 'woocommerce-reduction-categorie')
                  ) . '</p>';
            echo '</div>';
        }
    }
    
    // Récupérer la taxonomie de marque utilisée
    private function get_brand_taxonomy() {
        $possible_taxonomies = array('product_brand', 'pa_brand', 'brand', 'pwb-brand', 'yith_product_brand');
        
        foreach ($possible_taxonomies as $tax) {
            if (taxonomy_exists($tax)) {
                return $tax;
            }
        }
        
        return '';
    }
    
    // Ajouter une boîte d'information de réduction sur la page d'édition du produit
    public function add_discount_info_box() {
        add_meta_box(
            'wc_kategori_indirim_info',
            __('Informations sur la réduction par catégorie', 'woocommerce-reduction-categorie'),
            array($this, 'display_discount_info_box'),
            'product',
            'side',
            'high'
        );
    }
    
    // Contenu de la boîte d'information de réduction
    public function display_discount_info_box($post) {
        // Vérifier si la remise est activée
        if (!get_option('wc_kategori_indirim_enabled')) {
            echo '<p>' . __('La réduction par catégorie/marque n\'est pas activée actuellement.', 'woocommerce-reduction-categorie') . '</p>';
            return;
        }
        
        // Type de réduction
        $discount_type = get_option('wc_kategori_indirim_type', 'category');
        $term_id = ($discount_type === 'category') ? get_option('wc_kategori_indirim_category') : get_option('wc_kategori_indirim_brand');
        $taxonomy = ($discount_type === 'category') ? 'product_cat' : $this->get_brand_taxonomy();
        $type_text = ($discount_type === 'category') ? __('catégorie', 'woocommerce-reduction-categorie') : __('marque', 'woocommerce-reduction-categorie');
        
        // Si aucun terme n'est sélectionné, ne pas afficher les informations
        if (empty($term_id) || empty($taxonomy)) {
            echo '<p>' . sprintf(__('Aucune %s n\'a été sélectionnée.', 'woocommerce-reduction-categorie'), $type_text) . '</p>';
            return;
        }
        
        // Vérifier si le produit appartient à ce terme
        if (has_term($term_id, $taxonomy, $post->ID)) {
            $discount_percentage = floatval(get_option('wc_kategori_indirim_amount', 20));
            $term = get_term($term_id, $taxonomy);
            
            echo '<p>' . sprintf(__('Ce produit est dans la %s "%s" et bénéficie actuellement d\'une réduction de %s%%.', 'woocommerce-reduction-categorie'), 
                    $type_text, 
                    $term->name, 
                    $discount_percentage) . '</p>';
            
            // Afficher le prix normal et réduit du produit
            $product = wc_get_product($post->ID);
            $regular_price = floatval($product->get_regular_price());
            $sale_price = floatval($product->get_sale_price());
            
            // Si nous n'avons pas de prix de vente, le calculer
            if (empty($sale_price) && $regular_price > 0) {
                $sale_price = $regular_price - ($regular_price * ($discount_percentage / 100));
                $sale_price = round($sale_price, 2);
            }
            
            if (!empty($regular_price)) {
                echo '<p>' . __('Prix original:', 'woocommerce-reduction-categorie') . ' ' . wc_price($regular_price) . '</p>';
                echo '<p>' . __('Prix réduit:', 'woocommerce-reduction-categorie') . ' ' . wc_price($sale_price) . '</p>';
                
                if (empty($product->get_sale_price())) {
                    echo '<p><strong>' . __('Attention:', 'woocommerce-reduction-categorie') . '</strong> ' . __('La réduction sera appliquée après enregistrement des paramètres de réduction.', 'woocommerce-reduction-categorie') . '</p>';
                }
            }
        } else {
            $term = get_term($term_id, $taxonomy);
            echo '<p>' . sprintf(__('Ce produit n\'est pas dans la %s "%s". Aucune réduction n\'est appliquée.', 'woocommerce-reduction-categorie'), 
                    $type_text, 
                    $term->name) . '</p>';
        }
    }
}

// Démarrer l'extension si WooCommerce est actif
function wckategori_indirim_init() {
    if (wckategori_indirim_check_woocommerce()) {
        $GLOBALS['wc_kategori_indirim'] = WC_Kategori_Indirim::instance();
    }
}
add_action('plugins_loaded', 'wckategori_indirim_init');

// Ajouter les réglages par défaut lors de l'activation de l'extension
register_activation_hook(__FILE__, 'wckategori_indirim_activate');
function wckategori_indirim_activate() {
    // Réglages par défaut
    add_option('wc_kategori_indirim_type', 'category');
    add_option('wc_kategori_indirim_amount', 20);
    add_option('wc_kategori_indirim_badge_text', '-20%');
    add_option('wc_kategori_indirim_badge_color', '#dd3333');
    add_option('wc_kategori_indirim_enabled', 0);
}

// Lors de la désactivation de l'extension, réinitialiser les prix
register_deactivation_hook(__FILE__, 'wckategori_indirim_deactivate');
function wckategori_indirim_deactivate() {
    // On va simplement supprimer toutes les réductions appliquées par notre extension
    global $wpdb;
    
    // Récupérer tous les produits qui ont notre marque de réduction
    $marked_products = $wpdb->get_col(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_kategori_indirim_applied' AND meta_value = '1'"
    );
    
    // Supprimer les réductions de tous ces produits
    foreach ($marked_products as $product_id) {
        // Supprimer le prix de vente
        delete_post_meta($product_id, '_sale_price');
        
        // Rétablir le prix original
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        if ($regular_price) {
            update_post_meta($product_id, '_price', $regular_price);
        }
        
        // Supprimer notre marque
        delete_post_meta($product_id, '_wc_kategori_indirim_applied');
    }
}

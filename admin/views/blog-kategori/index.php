<?php
/**
 * Standalone Blog Kategori manager — WordPress/WooCommerce style.
 * Listing, hierarchy, create, edit, delete in one page.
 */
$db = Database::getInstance();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

// Table config (so a similar produk-kategori file can be near-identical)
$TABLE   = 'blog_kategori';
$REL     = 'blog_kategori_rel';
$REL_COL = 'blog_id';       // FK in rel table pointing back to main item
$ITEM_TBL = 'blog';
$LABEL   = 'Artikel';
$BASE_URL_KEY = 'blog-kategori';
$BACK_URL = admin_url('?page=blog');

// ---- POST handlers ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token tidak valid.');
        redirect(admin_url("?page=$BASE_URL_KEY"));
    }
    $form_action = $_POST['_action'] ?? '';
    
    if ($form_action === 'delete') {
        $del_id = (int)($_POST['id'] ?? 0);
        if ($del_id > 0) {
            // Detach: re-parent children to NULL (don't orphan them silently — let admin re-parent)
            $db->execute("UPDATE $TABLE SET parent_id = NULL WHERE parent_id = ?", [$del_id]);
            // Remove relations (item-to-category links remain consistent)
            $db->execute("DELETE FROM $REL WHERE kategori_id = ?", [$del_id]);
            $db->execute("DELETE FROM $TABLE WHERE id = ?", [$del_id]);
            log_activity('delete', "Kategori blog ID $del_id", $user['id']);
            set_flash('success', '🗑️ Kategori berhasil dihapus.');
        }
        redirect(admin_url("?page=$BASE_URL_KEY"));
    }

    if ($form_action === 'create' || $form_action === 'edit') {
        $nama = trim($_POST['nama'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $parent_id = (int)($_POST['parent_id'] ?? 0) ?: null;
        $urutan = (int)($_POST['urutan'] ?? 0);
        if (!$nama) {
            set_flash('error', 'Nama kategori wajib diisi.');
            redirect(admin_url("?page=$BASE_URL_KEY&action=" . ($form_action==='edit'?"edit&id=$id":'create')));
        }
        if (!$slug) $slug = make_slug($nama);
        
        // Validate parent isn't itself or a descendant (prevent circular tree)
        if ($form_action === 'edit' && $parent_id) {
            if ($parent_id === $id) {
                set_flash('error', 'Parent tidak boleh dirinya sendiri.');
                redirect(admin_url("?page=$BASE_URL_KEY&action=edit&id=$id"));
            }
            $all = $db->fetchAll("SELECT id, parent_id FROM $TABLE");
            if (in_array($parent_id, get_descendant_ids($all, $id), true)) {
                set_flash('error', 'Tidak bisa memilih sub-kategori sebagai parent.');
                redirect(admin_url("?page=$BASE_URL_KEY&action=edit&id=$id"));
            }
        }

        // Ensure unique slug
        $base_slug = $slug;
        $i = 1;
        while (true) {
            $exists = $db->fetchOne("SELECT id FROM $TABLE WHERE slug = ? AND id != ?", [$slug, $id]);
            if (!$exists) break;
            $i++;
            $slug = $base_slug . '-' . $i;
        }

        if ($form_action === 'create') {
            $db->execute("INSERT INTO $TABLE (nama, slug, parent_id, urutan) VALUES (?,?,?,?)",
                [$nama, $slug, $parent_id, $urutan]);
            log_activity('create', "Kategori blog: $nama", $user['id']);
            set_flash('success', '✅ Kategori berhasil ditambahkan.');
        } else {
            $db->execute("UPDATE $TABLE SET nama=?, slug=?, parent_id=?, urutan=? WHERE id=?",
                [$nama, $slug, $parent_id, $urutan, $id]);
            log_activity('update', "Kategori blog: $nama", $user['id']);
            set_flash('success', '✅ Kategori berhasil diperbarui.');
        }
        redirect(admin_url("?page=$BASE_URL_KEY"));
    }
}

$csrf = generate_csrf();

include __DIR__ . '/../_kategori-shared.php';

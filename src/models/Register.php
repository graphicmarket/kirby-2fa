<?php

namespace graphicmarket\kirby2fa;

use Exception;
use Kirby\Database\Database;
use Kirby\Toolkit\F;

/**
 * Handle the users as registers in the database
 * 
 *
 * @author    Ronald Torres <ronald@graphic.market>
 */

class Register {
    /**
     * DB connection
     *
     * @var \Kirby\Database\Database;
     */
    protected $db;
    private $table = 'register';

    public function __construct() {
        $this->setupDB();
    }

    /**
     * Add a new register
     *
     * @param array $props
     * @return void
     */
    public function add(array $props) {

        if (!$props['user_id'] && !$props['user_id']) {
            throw new Exception("user_id and secret are needle to add.");
        }

        return $this->db->register()->insert($props);
    }

    public function delete(string $id) {
        return $this->db->register()->delete([
            'user_id' => $id,
        ]);
    }

    /**
     * Find a user in the database
     *
     * @param string $id
     * @return array|null
     */
    public function get(string $id): ?array{

        $q = $this->db->register()->select('*')->where([
            'user_id' => $id,
        ])->fetch('array')->first();

        return $q ?: null;
    }

    public function exist(string $id): bool {
        return $this->get($id) ? true : false;
    }

    /**
     * Prepare the database file
     *
     * @return void
     */
    private function setupDB() {
        
        $file = option('graphicmarket.kirby-2fa.database');

        if(is_callable($file)){
            $file = $file();
        }

        if (!F::exists($file)) {
            F::write($file, null);
        }
        // Connect to database
        $this->db = new Database([
            'type' => 'sqlite',
            'database' => $file,
        ]);

        if (!$this->db->validateTable($this->table)) {

            $this->db->createTable($this->table, [
                [
                    'type' => 'varchar',
                    'name' => 'user_id',
                ],
                [
                    'type' => 'varchar',
                    'name' => 'secret',
                ],
                [
                    'type' => 'timestamp',
                    'name' => 'created_at',
                ],
            ]);
        }

    }
}
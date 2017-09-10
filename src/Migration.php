<?php
/**
 * Author: Hzhihua
 * Date: 17-9-7
 * Time: 下午12:18
 * Hzhihua <1044144905@qq.com>
 */

namespace hzhihua\dump;

use yii\db\Exception;
use yii\helpers\Console;
use hzhihua\dump\models\Output;
/**
 * Migration class file.
 * all migration file generated extends this file
 */
class Migration extends \yii\db\Migration
{
    /**
     * @var string table additional options
     */
    public $tableOptions = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            // use utf8mb4 may cause some errors that "Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes" for "ADD UNIQUE INDEX"
            $this->tableOptions = " ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_unicode_ci ";
        }
    }

    /**
     * @return bool return true if applying success or throw new exception of db
     * @throws \yii\db\Exception
     */
    public function up()
    {
        Output::stdout("*** beginTransaction\n", 0, Console::FG_YELLOW);
        $transaction = $this->db->beginTransaction();

        try {
            $this->safeUp();
            Output::stdout("*** commit Transaction\n", 0, Console::FG_YELLOW);
            $transaction->commit();
            return true;

        } catch (Exception $e) {

            try {
                Output::stdout("\n*** running safeDown\n", 0, Console::FG_YELLOW);
                $this->safeDown();

            } catch (Exception $_e) {

            }

            Output::stdout("\n*** rollBackTransaction", 0, Console::FG_YELLOW);
            $transaction->rollBack();

            Output::stdout("\n*** Error: ", 1, Console::FG_RED);
            throw new Exception($e->getMessage());
        }

    }
}
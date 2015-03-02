<?php

abstract class Migration extends CDbMigration {

    /**
     * Override that method to upgrade database
     * @return string - Sql query
     */
    public abstract function upgrade();

    /**
     * Override that method to downgrade database
     * @return string - Sql query
     */
    public function downgrade() {
        echo get_class($this)." does not support migration down.\n";
        print null;
    }

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from {@link up} in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of {@link up} if the DB logic
     * needs to be within a transaction.
     * @return boolean Returning false means, the migration will not be applied and
     * the transaction will be rolled back.
     * @since 1.1.7
     */
    public function safeUp() {
        $sql = $this->upgrade();
        if (!empty($sql)) {
            $this->getDbConnection()->createCommand()->execute();
        }
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from {@link down} in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of {@link up} if the DB logic
     * needs to be within a transaction.
     * @return boolean Returning false means, the migration will not be applied and
     * the transaction will be rolled back.
     * @since 1.1.7
     */
    public function safeDown() {
        $sql = $this->downgrade();
        if (!empty($sql)) {
            $this->getDbConnection()->createCommand()->execute();
        }
    }
} 
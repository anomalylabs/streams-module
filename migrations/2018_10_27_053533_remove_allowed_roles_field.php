<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

/**
 * Class RemoveAllowedRolesField
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class RemoveAllowedRolesField extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$field = $this->fields()->findBySlugAndNamespace('allowed_roles', 'streams')) {
            return;
        }

        $this->fields()->delete($field);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

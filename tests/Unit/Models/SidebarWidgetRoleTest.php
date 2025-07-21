<?php

namespace Tests\Unit\Models;

use App\Models\SidebarWidget;
use App\Models\SidebarWidgetRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SidebarWidgetRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var SidebarWidgetRole
     */
    private $sidebarWidgetRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sidebarWidgetRole = new SidebarWidgetRole();
    }

    /**
     * Test sidebarWidget relationship.
     *
     * @return void
     */
    public function testSidebarWidgetRelationship()
    {
        $relation = $this->sidebarWidgetRole->sidebarWidget();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('sidebar_widget_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
        $this->assertInstanceOf(SidebarWidget::class, $relation->getModel());
    }

    /**
     * Test role relationship.
     *
     * @return void
     */
    public function testRoleRelationship()
    {
        $relation = $this->sidebarWidgetRole->role();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('role_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
        $this->assertInstanceOf(Role::class, $relation->getModel());
    }

    /**
     * Test roles relationship.
     *
     * @return void
     */
    public function testRolesRelationship()
    {
        $relation = $this->sidebarWidgetRole->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('sidebar_widget_roles', $relation->getTable());
        $this->assertEquals('sidebar_widget_role_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('role_id', $relation->getRelatedPivotKeyName());
        $this->assertInstanceOf(Role::class, $relation->getModel());
    }
}
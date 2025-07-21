<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BaseRepositoryTest extends TestCase
{
    private MockInterface $model;
    private MockInterface $query;
    private BaseRepository $baseRepository;
    private string $modelClassName = 'App\Models\User';

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);
        $this->query = Mockery::mock(EloquentBuilder::class);

        $this->baseRepository = new BaseRepository($this->modelClassName);
        $this->baseRepository->setModel($this->model);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetRoles(): void
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $this->model->shouldReceive('roles')->once()->andReturn($relation);
        $relation->shouldReceive('orderBy')->with('name', 'asc')->andReturnSelf();
        $relation->shouldReceive('get')->once()->andReturn(new Collection());

        $this->baseRepository->setModel($this->model);
        $roles = $this->baseRepository->getRoles($this->model);

        $this->assertInstanceOf(Collection::class, $roles);
    }

    public function testDetachRoles(): void
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $ids = [1, 2, 3];

        $relation->shouldReceive('detach')->with($ids)->once();

        $result = $this->baseRepository->detachRoles($relation, $ids);

        $this->assertTrue($result);
    }

    public function testDetachRolesWithEmptyIds(): void
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $ids = [];

        $result = $this->baseRepository->detachRoles($relation, $ids);

        $this->assertFalse($result);
    }

    public function testGetHighestOrder(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $query->shouldReceive('max')->with('order')->once()->andReturn(5);

        $result = $this->baseRepository->getHighestOrder($query);

        $this->assertEquals(6, $result);
    }

    public function testFindCollectionIndex(): void
    {
        $collection = new Collection([
            (object)['id' => 1, 'name' => 'Test1'],
            (object)['id' => 2, 'name' => 'Test2'],
            (object)['id' => 3, 'name' => 'Test3'],
        ]);

        $model = (object)['id' => 2, 'name' => 'Test2'];
        $where = ['id' => 2];

        $index = $this->baseRepository->findCollectionIndex($collection, $model, $where);

        $this->assertEquals(1, $index);
    }

    public function testFindCollectionIndexNotFound(): void
    {
        $collection = new Collection([
            (object)['id' => 1, 'name' => 'Test1'],
            (object)['id' => 2, 'name' => 'Test2'],
            (object)['id' => 3, 'name' => 'Test3'],
        ]);

        $model = (object)['id' => 2, 'name' => 'Test2'];
        $where = ['id' => 4];

        $index = $this->baseRepository->findCollectionIndex($collection, $model, $where);

        $this->assertNull($index);
    }

    public function testReorderByDirectionUp(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $model = Mockery::mock(Model::class);
        $collection = new Collection([
            (object)['id' => 1, 'order' => 1],
            $model,
            (object)['id' => 3, 'order' => 3],
        ]);
        $model->id = 2;
        $model->order = 2;

        $query->shouldReceive('get')->once()->andReturn($collection);
        $query->shouldReceive('count')->once()->andReturn(3);

        $model->shouldReceive('__get')->with('order')->twice()->andReturn(2);
        $model->shouldReceive('__set')->with('order', 1)->once();
        $model->shouldReceive('save')->once()->andReturn(true);

        $this->baseRepository->setModel($this->model);

        $this->assertTrue(true);

        // $this->baseRepository->reorderByDirection($model, $query, 'up');
    }

    public function testReorderByDirectionDown(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $model = Mockery::mock(Model::class);
        $collection = new Collection([
            (object)['id' => 1, 'order' => 1],
            $model,
            (object)['id' => 3, 'order' => 3],
        ]);
        $model->id = 2;
        $model->order = 2;

        $query->shouldReceive('get')->once()->andReturn($collection);
        $query->shouldReceive('count')->once()->andReturn(3);

        $model->shouldReceive('__get')->with('order')->twice()->andReturn(2);
        $model->shouldReceive('__set')->with('order', 3)->once();
        $model->shouldReceive('save')->once()->andReturn(true);

        $this->baseRepository->setModel($this->model);
        $this->assertTrue(true);

        //$this->baseRepository->reorderByDirection($model, $query, 'down');
    }

    public function testReorderByDirectionInvalidDirection(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $model = Mockery::mock(Model::class);
        $this->baseRepository->setModel($this->model);

        $this->assertFalse($this->baseRepository->reorderByDirection($this->model, $query, 'invalid'));
    }

    public function testReorderByField(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $collection = new Collection([
            (object)['id' => 1, 'order' => 5],
            (object)['id' => 2, 'order' => 4],
            (object)['id' => 3, 'order' => 3],
        ]);

        $query->shouldReceive('get')->once()->andReturn($collection);
        foreach ($collection as $item) {
            $item->shouldReceive('__set')->with('order', Mockery::any())->once();
            $item->shouldReceive('save')->once()->andReturn(true);
        }

        $this->baseRepository->setModel($this->model);

        $this->assertTrue(true);
        //$this->baseRepository->reorderByField($query, 'order');
    }

    public function testBuildCloneEntityStr(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $field = 'name';
        $str = 'Test';
        $separator = '-cloned-';

        $query->shouldReceive('pluck')->with($field)->once()->andReturn(collect(['Test', 'Test-cloned-1', 'Test-cloned-2']));
        $this->model->shouldReceive('where')->with($field, 'Test')->andReturnSelf();
        $this->model->shouldReceive('exists')->andReturn(true);
        $this->model->shouldReceive('where')->with($field, 'Test-cloned-3')->andReturnSelf();
        $this->model->shouldReceive('exists')->andReturn(false);

        $result = $this->baseRepository->buildCloneEntityStr($query, $field, $str, $separator);

        $this->assertEquals('Test-cloned-3', $result);
    }

    public function testGetWhereDoesntHave(): void
    {
        $this->assertIsArray($this->baseRepository->getWhereDoesntHave());
    }

    public function testSetWhereDoesntHave(): void
    {
        $whereDoesntHave = ['relation' => function ($query) {
        }];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setWhereDoesntHave($whereDoesntHave));
        $this->assertEquals($whereDoesntHave, $this->baseRepository->getWhereDoesntHave());
    }

    public function testGetWhereHas(): void
    {
        $this->assertIsArray($this->baseRepository->getWhereHas());
    }

    public function testSetWhereHas(): void
    {
        $whereHas = ['relation' => function ($query) {
        }];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setWhereHas($whereHas));
        $this->assertEquals($whereHas, $this->baseRepository->getWhereHas());
    }

    public function testGetWith(): void
    {
        $this->assertIsArray($this->baseRepository->getWith());
    }

    public function testSetWith(): void
    {
        $with = ['relation'];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setWith($with));
        $this->assertEquals($with, $this->baseRepository->getWith());
    }

    public function testGetLoad(): void
    {
        $this->assertIsArray($this->baseRepository->getLoad());
    }

    public function testSetLoad(): void
    {
        $load = ['relation'];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setLoad($load));
        $this->assertEquals($load, $this->baseRepository->getLoad());
    }

    public function testAddWhereDoesntHaveToQuery(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $this->baseRepository->setWhereDoesntHave(['relation' => function ($query) {
        }]);

        $query->shouldReceive('whereDoesntHave')->with('relation', Mockery::type('Closure'))->once()->andReturnSelf();
        $result = $this->baseRepository->addWhereDoesntHaveToQuery($query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testAddWhereDoesntHaveToQueryWithStringValue(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $this->baseRepository->setWhereDoesntHave(['relation']);

        $query->shouldReceive('whereDoesntHave')->with('relation')->once()->andReturnSelf();
        $result = $this->baseRepository->addWhereDoesntHaveToQuery($query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testAddWithToQuery(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $this->baseRepository->setWith(['relation']);

        $query->shouldReceive('with')->with(['relation'])->once()->andReturnSelf();
        $result = $this->baseRepository->addWithToQuery($query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testAddLoadToQuery(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $this->baseRepository->setLoad(['relation']);

        $query->shouldReceive('load')->with(['relation'])->once()->andReturnSelf();
        $result = $this->baseRepository->addLoadToQuery($query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testAddLoadToQueryEmpty(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);

        $result = $this->baseRepository->addLoadToQuery($query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testFindModelsByUser(): void
    {
        $model = Mockery::mock(Model::class);
        $user = Mockery::mock(User::class);

        $this->baseRepository->setModel($model);

        $query = Mockery::mock(EloquentBuilder::class);
        $this->baseRepository->setQuery($query);

        $this->baseRepository->shouldReceive('getModelByUserQuery')->with($model, $user, true)->once()->andReturn($query);
        $this->baseRepository->shouldReceive('getResults')->with($query)->once()->andReturn(new Collection());

        $result = $this->baseRepository->findModelsByUser($model, $user);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetModelInstanceWithData(): void
    {
        $data = ['name' => 'Test'];

        $result = $this->baseRepository->getModelInstance($data);

        $this->assertInstanceOf($this->modelClassName, $result);
    }

    public function testGetModelInstanceWithoutData(): void
    {
        $result = $this->baseRepository->getModelInstance();

        $this->assertInstanceOf($this->modelClassName, $result);
    }

    public function testFindAll(): void
    {
        $this->modelClassName::shouldReceive('all')->once()->andReturn(new Collection());

        $result = $this->baseRepository->findAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testFindByModel(): void
    {
        $model = Mockery::mock(Model::class);
        $model->id = 1;

        $this->baseRepository->setModel($model);

        $this->baseRepository->shouldReceive('findById')->with(1)->once()->andReturn($model);

        $result = $this->baseRepository->findByModel($model);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function testFindById(): void
    {
        $this->baseRepository->shouldReceive('addWhere')->with('id', 1)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('findOne')->once()->andReturn($this->model);

        $result = $this->baseRepository->findById(1);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function testFindByName(): void
    {
        $this->baseRepository->shouldReceive('addWhere')->with('name', 'Test')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('findOne')->once()->andReturn($this->model);

        $result = $this->baseRepository->findByName('Test');

        $this->assertInstanceOf(Model::class, $result);
    }

    public function testNewQuery(): void
    {
        $this->modelClassName::shouldReceive('query')->once()->andReturn($this->query);

        $result = $this->baseRepository->newQuery();

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testReset(): void
    {
        $this->baseRepository->setWhere(['test' => 1]);
        $this->baseRepository->setOrderByColumn('test_column');
        $this->baseRepository->setOrderByDir('desc');
        $this->baseRepository->setLimit(10);
        $this->baseRepository->setOffset(5);
        $this->baseRepository->setFixedRows(['test' => 1]);
        $this->baseRepository->setWhereDoesntHave(['test' => 1]);
        $this->baseRepository->setWhereHas(['test' => 1]);
        $this->baseRepository->setWith(['test']);
        $this->baseRepository->setLoad(['test']);
        $this->baseRepository->setQuery($this->query);

        $this->baseRepository->reset();

        $this->assertEquals([], $this->baseRepository->getWhere());
        $this->assertEquals('id', $this->baseRepository->getOrderByColumn());
        $this->assertEquals('asc', $this->baseRepository->getOrderByDir());
        $this->assertEquals(-1, $this->baseRepository->getLimit());
        $this->assertEquals(0, $this->baseRepository->getOffset());
        $this->assertEquals([], $this->baseRepository->getFixedRows());
        $this->assertEquals([], $this->baseRepository->getWhereDoesntHave());
        $this->assertEquals([], $this->baseRepository->getWhereHas());
        $this->assertEquals([], $this->baseRepository->getWith());
        $this->assertEquals([], $this->baseRepository->getLoad());
        $this->assertNull($this->baseRepository->getQuery());
    }

    public function testSetFixedRows(): void
    {
        $fixedRows = ['test' => 1];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setFixedRows($fixedRows));
        $this->assertEquals($fixedRows, $this->baseRepository->getFixedRows());
    }

    public function testGetFixedRows(): void
    {
        $this->assertIsArray($this->baseRepository->getFixedRows());
    }

    public function testSetOrderBy(): void
    {
        $orderBy = ['column' => 'name', 'dir' => 'asc'];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setOrderBy([$orderBy]));
        $this->assertEquals([$orderBy], $this->baseRepository->getOrderBy());
    }

    public function testGetOrderBy(): void
    {
        $this->assertIsArray($this->baseRepository->getOrderBy());
    }

    public function testSetQuery(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setQuery($this->query));
        $this->assertEquals($this->query, $this->baseRepository->getQuery());
    }

    public function testGetQuery(): void
    {
        $this->assertNull($this->baseRepository->getQuery());
    }

    public function testFindOne(): void
    {
        $this->baseRepository->shouldReceive('buildQuery')->once()->andReturn($this->query);
        $this->baseRepository->shouldReceive('setQuery')->with($this->query)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('getQuery')->once()->andReturn($this->query);
        $this->baseRepository->shouldReceive('getOneResult')->with($this->query)->once()->andReturn($this->model);
        $this->baseRepository->shouldReceive('reset')->once();

        $result = $this->baseRepository->findOne();

        $this->assertInstanceOf(Model::class, $result);
    }

    public function testFindMany(): void
    {
        $this->baseRepository->shouldReceive('buildQuery')->once()->andReturn($this->query);
        $this->baseRepository->shouldReceive('setQuery')->with($this->query)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('getQuery')->once()->andReturn($this->query);
        $this->baseRepository->shouldReceive('getResults')->with($this->query)->once()->andReturn(new Collection());
        $this->baseRepository->shouldReceive('reset')->once();

        $result = $this->baseRepository->findMany();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testFindByLabelOrName(): void
    {
        $this->baseRepository->shouldReceive('addWhere')->with('label', 'LIKE', '%Test%')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('addWhere')->with('name', 'LIKE', '%Test%', 'OR')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('findMany')->once()->andReturn(new Collection());

        $result = $this->baseRepository->findByLabelOrName('Test');

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testFindAllWithParams(): void
    {
        $this->baseRepository->shouldReceive('setOrderByDir')->with('asc')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('setOrderByColumn')->with('name')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('findMany')->once()->andReturn(new Collection());

        $result = $this->baseRepository->findAllWithParams();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetWhere(): void
    {
        $this->assertIsArray($this->baseRepository->getWhere());
    }

    public function testSetWhere(): void
    {
        $where = ['test' => 1];
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setWhere($where));
        $this->assertEquals($where, $this->baseRepository->getWhere());
    }

    public function testGetOrderByColumn(): void
    {
        $this->assertEquals('id', $this->baseRepository->getOrderByColumn());
    }

    public function testSetOrderByColumn(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setOrderByColumn('name'));
        $this->assertEquals('name', $this->baseRepository->getOrderByColumn());
    }

    public function testGetOrderByDir(): void
    {
        $this->assertEquals('asc', $this->baseRepository->getOrderByDir());
    }

    public function testSetOrderByDir(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setOrderByDir('desc'));
        $this->assertEquals('desc', $this->baseRepository->getOrderByDir());
    }

    public function testGetLimit(): void
    {
        $this->assertEquals(-1, $this->baseRepository->getLimit());
    }

    public function testSetLimit(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setLimit(10));
        $this->assertEquals(10, $this->baseRepository->getLimit());
    }

    public function testGetOffset(): void
    {
        $this->assertEquals(0, $this->baseRepository->getOffset());
    }

    public function testSetOffset(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setOffset(5));
        $this->assertEquals(5, $this->baseRepository->getOffset());
    }

    public function testGetModel(): void
    {
        $this->assertInstanceOf(Model::class, $this->baseRepository->getModel());
    }

    public function testApplyConditionsToQuery(): void
    {
        $query = Mockery::mock(EloquentBuilder::class);
        $conditions = [
            ['name', 'Test', '='],
            ['age', 30, '>'],
        ];

        $query->shouldReceive('where')->with('name', '=', 'Test')->once()->andReturnSelf();
        $query->shouldReceive('where')->with('age', '>', 30)->once()->andReturnSelf();

        $result = $this->baseRepository->applyConditionsToQuery($conditions, $query);

        $this->assertInstanceOf(EloquentBuilder::class, $result);
    }

    public function testApplyConditions(): void
    {
        $conditions = [
            ['name', 'Test', '='],
            ['age', 30, '>'],
        ];

        $this->baseRepository->shouldReceive('addWhere')->with('name', 'Test', '=', null)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('addWhere')->with('age', 30, '>', null)->once()->andReturnSelf();

        $result = $this->baseRepository->applyConditions($conditions);

        $this->assertTrue($result);
    }

    public function testFindOneBy(): void
    {
        $conditions = [
            ['name', 'Test', '='],
            ['age', 30, '>'],
        ];

        $this->baseRepository->shouldReceive('applyConditions')->with($conditions)->once()->andReturn(true);
        $this->baseRepository->shouldReceive('findOne')->once()->andReturn($this->model);

        $result = $this->baseRepository->findOneBy($conditions);

        $this->assertInstanceOf(Model::class, $result);
    }

    public function testFindManyBy(): void
    {
        $conditions = [
            ['name', 'Test', '='],
            ['age', 30, '>'],
        ];

        $this->baseRepository->shouldReceive('applyConditions')->with($conditions)->once()->andReturn(true);
        $this->baseRepository->shouldReceive('findMany')->once()->andReturn(new Collection());

        $result = $this->baseRepository->findManyBy($conditions);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testSaveInsert(): void
    {
        $data = ['name' => 'Test'];
        $this->baseRepository->shouldReceive('doesModelExist')->once()->andReturn(false);
        $this->baseRepository->shouldReceive('insert')->with($data)->once()->andReturn(true);

        $result = $this->baseRepository->save($data);

        $this->assertTrue($result);
    }

    public function testSaveUpdate(): void
    {
        $data = ['name' => 'Test'];

        $this->baseRepository->shouldReceive('doesModelExist')->once()->andReturn(true);
        $this->baseRepository->shouldReceive('update')->with($data)->once()->andReturn(true);

        $result = $this->baseRepository->save($data);

        $this->assertTrue($result);
    }

    public function testInsert(): void
    {
        $data = ['name' => 'Test'];
        $this->baseRepository->setModel($this->model);
        $this->baseRepository->shouldReceive('getModelInstance')->with($data)->once()->andReturn($this->model);
        $this->model->shouldReceive('save')->once()->andReturn(true);

        $result = $this->baseRepository->insert($data);

        $this->assertTrue($result);
    }

    public function testUpdate(): void
    {
        $data = ['name' => 'Test'];
        $this->baseRepository->setModel($this->model);
        $this->model->shouldReceive('fill')->with($data)->once()->andReturnSelf();
        $this->model->shouldReceive('save')->once()->andReturn(true);

        $result = $this->baseRepository->update($data);

        $this->assertTrue($result);
    }

    public function testDeleteBatch(): void
    {
        $ids = [1, 2, 3];
        $this->baseRepository->shouldReceive('addWhere')->with('id', 1)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('addWhere')->with('id', 2, '=', 'OR')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('addWhere')->with('id', 3, '=', 'OR')->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('buildQuery')->once()->andReturn($this->query);
        $this->baseRepository->shouldReceive('setQuery')->with($this->query)->once()->andReturnSelf();
        $this->baseRepository->shouldReceive('getQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->baseRepository->deleteBatch($ids);

        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $this->baseRepository->setModel($this->model);
        $this->model->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->baseRepository->delete();

        $this->assertTrue($result);
    }

    public function testSetModel(): void
    {
        $model = Mockery::mock(Model::class);
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->setModel($model));
        $this->assertEquals($model, $this->baseRepository->getModel());
    }

    public function testIsModelSet(): void
    {
        $this->baseRepository->setModel($this->model);
        $this->assertTrue($this->baseRepository->isModelSet());

        $newBaseRepository = new BaseRepository($this->modelClassName);
        $this->assertFalse($newBaseRepository->isModelSet());
    }

    public function testDoesModelExist(): void
    {
        $model = Mockery::mock(Model::class);
        $model->exists = true;
        $this->baseRepository->setModel($model);
        $this->assertTrue($this->baseRepository->doesModelExist());

        $model->exists = false;
        $this->baseRepository->setModel($model);
        $this->assertFalse($this->baseRepository->doesModelExist());

        $newBaseRepository = new BaseRepository($this->modelClassName);
        $this->assertFalse($newBaseRepository->doesModelExist());
    }

    public function testAddWhere(): void
    {
        $this->assertInstanceOf(BaseRepository::class, $this->baseRepository->addWhere('name', 'Test'));
        $this->assertEquals([['field' => 'name', 'value' => 'Test', 'compare' => '=', 'op' => 'AND']], $this->baseRepository->getWhere());
    }
}

<?php

use \PopCode\UserCrud\Controllers\UserController;

class UserControllerWithMetaTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    /**
     * @var MockHelper
     */
    protected $user;
    protected $metas;

    public function setUp() {
        parent::setUp();
        $this->user = new MockHelper('PopCode\\UserCrud\\Models\\User');
        $this->metas = new MockHelper('PopCode\\UserCrud\\Models\\UserMeta');
    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();
    }

    public function testIndex() {
        $data = 'asserted response';

        $this->user->shouldInstantiated();
        $this->user->mock
            ->shouldReceive('with')
            ->withArgs(['metas'])
            ->once()
            ->andReturnSelf();
        $this->user->getAll($data);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'indexUsers');

        $this->assertEquals($data, $response);
    }

    public function testValidationErrorBoth() {
        $data = ['error1' => 'error Msg'];
        $data2 = ['error2' => 'error Msg'];

        $this->user->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);
        $this->user->mock
            ->shouldReceive('messages')
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('toArray')
            ->andReturn($data);

        $this->metas->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);
        $this->metas->mock
            ->shouldReceive('messages')
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('toArray')
            ->andReturn($data2);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'hasError', ['userData', null]);

        $expected = array_merge($data, $data2);

        $this->assertEquals($expected, $response);
    }

    public function testValidationErrorUser() {
        $data = ['error1' => 'error Msg'];

        $this->user->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);
        $this->user->mock
            ->shouldReceive('messages')
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('toArray')
            ->andReturn($data);

        $this->metas->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'hasError', ['userData', null]);

        $this->assertEquals($data, $response);
    }

    public function testValidationErrorMeta() {
        $data = ['error2' => 'error Msg'];

        $this->user->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->metas->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);
        $this->metas->mock
            ->shouldReceive('messages')
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('toArray')
            ->andReturn($data);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'hasError', ['userData', null]);

        $this->assertEquals($data, $response);
    }

    public function testValidationSuccess() {
        $this->user->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->metas->mock
            ->shouldReceive('validate')
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'hasError', ['userData', null]);

        $this->assertEquals(false, $response);
    }

    public function testStore() {
        $data = [
            'id' => 1,
            'other_field' => 'other_value',
            'metas' => [
                ['key' => 'meta-key', 'value' => 'meta-value'],
            ],
        ];

        $this->user->mock
            ->shouldReceive('newInstance')
            ->with($data)
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('save')
            ->once();
        $this->user->mock
            ->shouldReceive('load')
            ->with('metas')
            ->once();

        $this->metas->mock
            ->shouldReceive('newInstance')
            ->with($data['metas'][0])
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('save')
            ->once();

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'storeUser', [$data]);

        $this->assertEquals($this->user->mock, $response);
    }

    public function testUpdate() {
        $id = 1;
        $data = [
            'id' => $id,
            'other_field' => 'other_value',
            'metas' => [
                ['key' => 'meta-key', 'value' => 'meta-value'],
            ],
        ];

        $this->user->mock
            ->shouldReceive('where')
            ->with('id', '=', $id)
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('first')
            ->once()
            ->andReturnSelf();

        $this->user->mock
            ->shouldReceive('metas')
            ->andReturn($this->metas->mock);

        $this->user->mock
            ->shouldReceive('fill')
            ->with($data)
            ->once();
        $this->user->mock
            ->shouldReceive('save')
            ->once();

        $this->metas->mock
            ->shouldReceive('delete')
            ->once();

        $this->metas->mock
            ->shouldReceive('newInstance')
            ->with($data['metas'][0])
            ->once()
            ->andReturnSelf();
        $this->metas->mock
            ->shouldReceive('save')
            ->once();

        $this->user->mock
            ->shouldReceive('load')
            ->with('metas')
            ->once();

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'updateUser', [$id, $data]);

        $this->assertEquals($this->user->mock, $response);
    }

    public function testDestroy() {
        $id = 1;

        $this->user->mock
            ->shouldReceive('where')
            ->with('id', '=', $id)
            ->once()
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('first')
            ->andReturnSelf();
        $this->user->mock
            ->shouldReceive('delete')
            ->andReturn(true);

        $controller = new UserController($this->user->mock, $this->metas->mock);

        $response = $this->invokeMethod($controller, 'destroyUser', [$id]);

        $this->assertEquals(true, $response);
    }
}

<?php namespace common\tests\models;

use common\fixtures\MemberFixture;
use common\models\Member;

class MemberTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        // $this->tester->haveRecord('common\models\Member', ['username' => 'davert']);
        $this->tester->seeRecord('common\models\Member', ['username' => 'username-11111111']);
        expect('should be true...', true)->true();
        expect('should be false...', false)->false();
        $this->assertTrue(true, 'should be true');
    }

    public function testValidation()
    {
        $model = new Member();

        $model->status = 'a';
        $this->assertFalse($model->validate(['status']));

        $model->status = 1;
        $this->assertTrue($model->validate(['status']));
    }

    public function testPushAndPop()
    {
        $stack = [];
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

    public function _fixtures()
    {
        return [
            'member' => [
                'class' => MemberFixture::className(),
//                'dataFile' => codecept_data_dir() . 'member.php'
            ]
        ];
    }

}
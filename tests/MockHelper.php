<?php

class MockHelper {

    /* @type \Mockery\Mock $mock */
    public $mock;

    /**
     * Create the mock
     *
     * @param string $className
     */
    public function __construct($className) {
        $this->mock = \Mockery::mock($className);
    }

    public static function unMock($className) {
    }

    /**
     * @return \Mockery\Mock
     */
    public function getMock() {
        return $this->mock;
    }

    /**
     * @return $this
     */
    public function shouldInstantiated() {
        $this->mock->shouldDeferMissing();
//        $this->mock->shouldReceive('__construct')
//            ->andReturn($this->mock);
        return $this;
    }

    /**
     * Test a query for a specified id
     *
     * @param mixed|bool $defaultResponse = false
     *
     * @return $this
     */
    public function getOne($defaultResponse = false) {
        $this->mock->shouldReceive('findOrFail')
            ->atLeast()
            ->once()
            ->andReturn($this->mock);
        $this->mock->shouldReceive('toArray')
            ->atLeast()
            ->once()
            ->andReturn($defaultResponse ?: []);
        return $this;
    }

    /**
     * Querying the first row
     *
     * @param bool|false $defaultResponse
     *
     * @return $this
     */
    public function getFirst($defaultResponse = false) {
        $this->mock->shouldReceive('where')
            ->atLeast()
            ->once()
            ->andReturn($this->mock);
        $this->mock->shouldReceive('first')
            ->atLeast()
            ->once()
            ->andReturn($this->mock);
        $this->mock->shouldReceive('toArray')
            ->atLeast()
            ->once()
            ->andReturn($defaultResponse ?: []);
        return $this;
    }

    /**
     * Test a full table query
     *
     * @param mixed|bool $defaultResponse = false
     *
     * @return $this
     */
    public function getAll($defaultResponse = false) {
        $this->mock->shouldReceive('all')
            ->andReturn($defaultResponse ?: []);
        return $this;
    }

    /**
     * Test a query with $filteringCyles where filtering
     *
     * @param mixed|bool $defaultResponse = false
     *
     * @return $this
     */
    public function getFiltered($defaultResponse = false) {
        $this->mock->shouldReceive('where')
            ->atLeast()
            ->once()
            ->andReturn($this->mock);
        $this->mock->shouldReceive('get')
            ->atLeast()
            ->once()
            ->andReturn($this->mock);
        $this->mock->shouldReceive('toArray')
            ->atLeast()
            ->once()
            // sample data...
            ->andReturn($defaultResponse ?: []);
        return $this;
    }

    public function push() {
        $this->mock->shouldReceive('fill');
        $this->mock->shouldReceive('save');
        return $this;
    }


    /**
     * @param string $attributeName
     * @param mixed $attributeValue
     *
     * @return $this
     */
    public function setAttribute($attributeName, $attributeValue) {
        $this->mock->shouldReceive('getAttribute')
            ->with($attributeName)
            ->andReturn($attributeValue);
        return $this;
    }
    public function set($attributeName, $attributeValue) {
        $this->setAttribute($attributeName, $attributeValue);
    }


    /* ----- ther helper functions without mock ----- */
    /**
     * Check recursively if two arrays have the same structure and data
     *
     * @param array $source an array to test equality to
     * @param array $toTest an array which values should be the same as in $source
     * @param array $skipKeys (optional) array of keys which value does not have to match
     *
     * @return bool
     */
    public static function checkIfNestedArraysEquals($source, $toTest, $skipKeys = []) {
        $keysInSource = array_keys($source);
        $keysInTest   = array_keys($toTest);
        $equals = count(array_diff($keysInSource, $keysInTest)) === 0 && count(array_diff($keysInTest, $keysInSource)) === 0;
        if(!$equals) {
            \Log::info([
                'error'  => 'test: checkIfNestedArrayEquals',
                'file'   => __FILE__,
                'line'   => __LINE__,
                'reason' => 'keys in the two arrays are not matches',
                'data'   => [
                    'src'  => $keysInSource,
                    'test' => $keysInTest
                ]
            ]);
            return false;
        }
        array_walk($toTest, function($value, $key) use (&$equals, $source, $skipKeys) {
            if(!$equals) return false;
            if(in_array($key, $skipKeys)) {
                return null;
            }
            if(is_array($value)) {
                if(!self::checkIfNestedArraysEquals($source[$key], $value, $skipKeys)) {
                    $equals = false;
                    return false;
                }
            } else {
                if($value != $source[$key]) {
                    $equals = false;
                    \Log::info([
                        'error'  => 'test: checkIfNestedArrayEquals',
                        'file'   => __FILE__,
                        'line'   => __LINE__,
                        'reason' => 'value mismatch',
                        'data'   => [
                            'src'  => $source[$key],
                            'test' => $value,
                            'key'  => $key
                        ]
                    ]);
                    return false;
                }
            }
            return null;
        });
        return $equals;
    }
}

<?php

namespace Cassandra\Type;

use Cassandra\Type;

/**
 * @requires extension cassandra
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefinesCollectionType()
    {
        $type = Type::collection(Type::varchar());
        $this->assertEquals("list", $type->name());
        $this->assertEquals("list<varchar>", (string) $type);
        $this->assertEquals(Type::varchar(), $type->valueType());
    }

    public function testCreatesCollectionFromValues()
    {
        $list = Type::collection(Type::varchar())
                    ->create("a", "b", "c", "d", "e");
        $this->assertEquals(array("a", "b", "c", "d", "e"), $list->values());
        $this->assertEquals("a", $list->get(0));
        $this->assertEquals("b", $list->get(1));
        $this->assertEquals("c", $list->get(2));
        $this->assertEquals("d", $list->get(3));
        $this->assertEquals("e", $list->get(4));
    }

    public function testCreatesEmptyCollection()
    {
        $list = Type::collection(Type::varchar())->create();
        $this->assertEquals(0, count($list));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage argument must be a string, '1' given
     */
    public function testPreventsCreatingCollectionWithUnsupportedTypes()
    {
        Type::collection(Type::varchar())->create(1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage type must be Cassandra\Type::varchar(),
     *                           Cassandra\Type::text(), Cassandra\Type::blob(),
     *                           Cassandra\Type::ascii(), Cassandra\Type::bigint(),
     *                           Cassandra\Type::counter(), Cassandra\Type::int(),
     *                           Cassandra\Type::varint(), Cassandra\Type::boolean(),
     *                           Cassandra\Type::decimal(), Cassandra\Type::double(),
     *                           Cassandra\Type::float(), Cassandra\Type::inet(),
     *                           Cassandra\Type::timestamp(), Cassandra\Type::uuid(),
     *                           Cassandra\Type::timeuuid(), Cassandra\Type::map(),
     *                           Cassandra\Type::set(), Cassandra\Type::collection(),
     *                           Cassandra\Type::tuple() or Cassandra\Type::udt(),
     *                           an instance of Cassandra\Type\UnsupportedType given
     */
    public function testPreventsDefiningCollectionsWithUnsupportedTypes()
    {
        Type::collection(new UnsupportedType());
    }

    /**
     * @dataProvider equalTypes
     */
    public function testCompareEquals($type1, $type2)
    {
        $this->assertEquals($type1, $type2);
        $this->assertTrue($type1 == $type2);
    }

    public function equalTypes()
    {
        return array(
            array(Type::collection(Type::int()),
                  Type::collection(Type::int())),
            array(Type::collection(Type::collection(Type::int())),
                  Type::collection(Type::collection(Type::int()))),
            array(Type::collection(Type::set(Type::int())),
                  Type::collection(Type::set(Type::int()))),
        );
    }

    /**
     * @dataProvider notEqualTypes
     */
    public function testCompareNotEquals($type1, $type2)
    {
        $this->assertNotEquals($type1, $type2);
        $this->assertFalse($type1 == $type2);
    }

    public function notEqualTypes()
    {
        return array(
            array(Type::collection(Type::varchar()),
                  Type::collection(Type::int())),
            array(Type::collection(Type::collection(Type::varchar())),
                  Type::collection(Type::collection(Type::int()))),
            array(Type::collection(Type::collection(Type::int())),
                  Type::collection(Type::set(Type::int()))),
        );
    }
}

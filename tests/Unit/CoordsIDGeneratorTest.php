<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Utilities\SCIO\CoordsIDGenerator;

class CoordsIDGeneratorTest extends TestCase
{
    /** @test */
    public function it_generates_an_identifier()
    {
        $coords = new CoordsIDGenerator([
            [
                [
                    64.171142578125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    34.94899072578227
                ]
            ]
        ]);

        $this->assertEquals(
            'a4c6c75f2ac38a8a3c9ae437f5c53843f8b9327c585e2899fcc7c57ee189700f',
            $coords->getId()
        );
    }

    /** @test */
    public function it_generates_a_different_identifier_for_different_coordinates()
    {
        $coords1 = new CoordsIDGenerator([
            [
                [
                    64.171142578125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    34.94899072578227
                ]
            ]
        ]);

        $coords2 = new CoordsIDGenerator([
            [
                [
                    64.171142578125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    33.90689555128866
                ]
            ]
        ]);

        $this->assertNotEquals($coords1->getId(), $coords2->getId());
    }

    /** @test */
    public function it_generates_the_same_identifier_for_the_same_coordinates()
    {
        $coords1 = new CoordsIDGenerator([
            [
                [
                    64.171142578125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    34.94899072578227
                ]
            ]
        ]);

        $coords2 = new CoordsIDGenerator([
            [
                [
                    64.171142578125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    33.90689555128866
                ],
                [
                    66.90673828125,
                    34.94899072578227
                ]
            ]
        ]);

        $this->assertEquals($coords1->getId(), $coords2->getId());
    }

    /** @test */
    public function it_generates_the_same_identifier_for_the_same_nested_coordinates()
    {
        $coords1 = new CoordsIDGenerator(
            [
                [
                    [
                        64.171142578125,
                        33.90689555128866
                    ],
                    [
                        66.90673828125,
                        33.90689555128866
                    ],
                    [
                        66.90673828125,
                        34.94899072578227
                    ]
                ]
            ]
        );

        $coords2 = new CoordsIDGenerator(
            [
                [
                    [
                        64.171142578125,
                        33.90689555128866
                    ],
                    [
                        66.90673828125,
                        33.90689555128866
                    ],
                    [
                        66.90673828125,
                        34.94899072578227
                    ]
                ]
            ]
        );

        $this->assertEquals($coords1->getId(), $coords2->getId());
    }
}

<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Test case.
 *
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class Tx_Seminars_Tests_Unit_Model_SkillTest extends Tx_Phpunit_TestCase
{
    /**
     * @var Tx_Seminars_Model_Skill
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new Tx_Seminars_Model_Skill();
    }

    /**
     * @test
     */
    public function setTitleWithEmptyTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'The parameter $title must not be empty.'
        );

        $this->fixture->setTitle('');
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $this->fixture->setTitle('Superhero');

        self::assertEquals(
            'Superhero',
            $this->fixture->getTitle()
        );
    }

    /**
     * @test
     */
    public function getTitleWithNonEmptyTitleReturnsTitle()
    {
        $this->fixture->setData(array('title' => 'Superhero'));

        self::assertEquals(
            'Superhero',
            $this->fixture->getTitle()
        );
    }
}

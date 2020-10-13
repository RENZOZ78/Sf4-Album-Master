<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\Record;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NoteFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData()
    {
        $this->createMany(400, 'note', function () {
            /** @var Record $record */
            $record = $this->getRandomReference('record');

            return (new Note())
                ->setAuthor($this->getRandomReference('user_user'))
                ->setRecord($record)
                ->setValue($this->faker->numberBetween(0, 10))
                ->setComment($this->faker->optional()->realText())
                ->setCreatedAt($this->faker->dateTimeBetween($record->getReleasedAt()))
            ;
        });
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            RecordFixtures::class,
        ];
    }
}
<?php

namespace Goralys\Tests\Unit\Core;

use Goralys\App\Subjects\Services\SubjectsUsernameManager;
use Goralys\Core\Subjects\Services\GetSubjectsService;
use Goralys\Core\Utils\User\Services\UsernameFormatterService;
use Goralys\Tests\Fakes\FakeGoralysLogger;
use Goralys\Tests\Fakes\FakeSubjectsRepository;
use mysqli_result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetSubjectsServiceTest extends TestCase
{
    private FakeGoralysLogger $logger;
    private FakeSubjectsRepository $repo;
    private UsernameFormatterService $formatter;
    private SubjectsUsernameManager $usernameManager;
    private GetSubjectsService $service;
    private mysqli_result&MockObject $mysqliResult;

    protected function setUp(): void
    {
        $this->logger = new FakeGoralysLogger();
        $this->repo = new FakeSubjectsRepository();
        $this->formatter = new UsernameFormatterService();
        $this->usernameManager = new SubjectsUsernameManager($this->logger);
        $this->mysqliResult = $this->createMock(mysqli_result::class);

        $this->service = new GetSubjectsService(
            $this->logger,
            $this->repo,
            $this->formatter,
            $this->usernameManager
        );
    }

    protected function tearDown(): void
    {
        unset($this->logger);
        unset($this->repo);
        unset($this->formatter);
        unset($this->usernameManager);
        unset($this->mysqliResult);
        unset($this->service);
    }

    public function testGetAllSubjects()
    {
        $subjects = [
                [
                        'teachers'       => 'j.doe1',
                        'student'        => 'e.doe3',
                        'topic'          => 'Maths',
                        'subject'        => 'Étude des fonctions',
                        'last_rejected'  => null,
                        'subject_status' => 3,
                        'draftPath'      => "/path/to/draft"
                ],
                [
                        'teachers'       => 'm.smith2',
                        'student'        => 'e.doe3',
                        'topic'          => 'Physique',
                        'subject'        => 'Ondes et interférences',
                        'last_rejected'  => null,
                        'subject_status' => 1,
                ],
                [
                        'teachers'       => 'j.doe1',
                        'student'        => 'l.dupont4',
                        'topic'          => 'Informatique',
                        'comment'        => "foo",
                        'subject'        => 'Algorithmes de tri',
                        'last_rejected'  => 'Algorithmes de tri',
                        'subject_status'         => 2,
                ],
                [
                        'teachers'       => 'm.smith2',
                        'student'        => 'l.dupont4',
                        'topic'          => 'Sciences',
                        'subject'        => 'Intelligence artificielle',
                        'last_rejected'  => null,
                        'subject_status'         => 3,
                ],
                null
        ];

        $expected = [
                [
                        'student'        => 'DOE E.',
                        'subject'        => 'Étude des fonctions',
                        'status'         => 'approved',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Maths',
                        'teacher'        => 'DOE J.',
                        'hasDraft'       => false,
                ],
                [
                        'student'        => 'DOE E.',
                        'subject'        => 'Ondes et interférences',
                        'status'         => 'submitted',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Physique',
                        'teacher'        => 'SMITH M.',
                        'hasDraft'       => false,
                ],
                [
                        'student'        => 'DUPONT L.',
                        'subject'        => 'Algorithmes de tri',
                        'status'         => 'rejected',
                        'comment'        => "foo",
                        'lastRejected'   => 'Algorithmes de tri',
                        'topic'          => 'Informatique',
                        'teacher'        => "DOE J.",
                        'hasDraft'       => false,
                ],
                [
                        'student'        => 'DUPONT L.',
                        'subject'        => 'Intelligence artificielle',
                        'status'         => 'approved',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Sciences',
                        'teacher'        => 'SMITH M.',
                        'hasDraft'       => false,
                ],
        ];

        $this->mysqliResult->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(...$subjects);
        $this->repo->setGetResult($this->mysqliResult);

        // Preparing the data
        $json = json_encode($this->service->getAllSubjects(), JSON_UNESCAPED_UNICODE);
        $actual = json_decode($json, true);

        foreach ($actual as &$s) {
            unset($s['studentToken'], $s['teacherToken']);
        }
        unset($s);

        self::assertSame($expected, $actual);
    }

    public function testGetStudentSubjects()
    {
        $subjects = [
                [
                        'teachers'       => 'j.doe1',
                        'student'        => 'e.doe3',
                        'topic'          => 'Maths',
                        'subject'        => 'Étude des fonctions',
                        'last_rejected'  => null,
                        'subject_status' => 3,
                        'draftPath'      => "/path/to/draft"
                ],
                [
                        'teachers'       => 'm.smith2',
                        'student'        => 'e.doe3',
                        'topic'          => 'Physique',
                        'subject'        => 'Ondes et interférences',
                        'last_rejected'  => null,
                        'subject_status' => 1,
                ],
                null
        ];

        $expected = [
                [
                        'student'        => 'DOE E.',
                        'subject'        => 'Étude des fonctions',
                        'status'         => 'approved',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Maths',
                        'teacher'        => 'DOE J.',
                        'hasDraft'       => false,
                ],
                [
                        'student'        => 'DOE E.',
                        'subject'        => 'Ondes et interférences',
                        'status'         => 'submitted',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Physique',
                        'teacher'        => 'SMITH M.',
                        'hasDraft'       => false,
                ],
        ];

        $this->mysqliResult->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(...$subjects);
        $this->repo->setGetResult($this->mysqliResult);

        // Preparing the data
        $json = json_encode($this->service->getStudentSubjects("e.doe3"), JSON_UNESCAPED_UNICODE);
        $actual = json_decode($json, true);

        foreach ($actual as &$s) {
            unset($s['studentToken'], $s['teacherToken']);
        }
        unset($s);

        self::assertSame($expected, $actual);
    }

    public function testGetTeacherSubjects()
    {
        $subjects = [
                [
                        'teachers'       => 'j.doe1',
                        'student'        => 'e.doe3',
                        'topic'          => 'Maths',
                        'subject'        => 'Étude des fonctions',
                        'last_rejected'  => null,
                        'subject_status' => 3,
                        'draftPath'      => "/path/to/draft"
                ],
                [
                        'teachers'       => 'j.doe1',
                        'student'        => 'l.dupont4',
                        'topic'          => 'Informatique',
                        'comment'        => "foo",
                        'subject'        => 'Algorithmes de tri',
                        'last_rejected'  => 'Algorithmes de tri',
                        'subject_status'         => 2,
                ],
                null
        ];

        $expected = [
                [
                        'student'        => 'DOE E.',
                        'subject'        => 'Étude des fonctions',
                        'status'         => 'approved',
                        'comment'        => "",
                        'lastRejected'   => "",
                        'topic'          => 'Maths',
                        'teacher'        => 'DOE J.',
                        'hasDraft'       => true,
                ],
                [
                        'student'        => 'DUPONT L.',
                        'subject'        => 'Algorithmes de tri',
                        'status'         => 'rejected',
                        'comment'        => "foo",
                        'lastRejected'   => 'Algorithmes de tri',
                        'topic'          => 'Informatique',
                        'teacher'        => "DOE J.",
                        'hasDraft'       => false,
                ],
        ];

        $this->mysqliResult->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(...$subjects);
        $this->repo->setGetResult($this->mysqliResult);

        // Preparing the data
        $json = json_encode($this->service->getTeacherSubjects("j.doe1"), JSON_UNESCAPED_UNICODE);
        $actual = json_decode($json, true);

        foreach ($actual as &$s) {
            unset($s['studentToken'], $s['teacherToken']);
        }
        unset($s);

        self::assertSame($expected, $actual);
    }
}

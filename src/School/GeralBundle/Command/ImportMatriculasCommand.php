<?php
/**
 * Created by PhpStorm.
 * User: ayata
 * Date: 30/01/18
 * Time: 14:26
 */

namespace School\GeralBundle\Command;

use School\MatriculaBundle\Entity\MatriculaAluno;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

class ImportMatriculasCommand extends Command
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('school:import:registrations')
            ->setDescription('Imports registers from csv')
            ->setHelp('This command allows you to import registers to the system')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $io = new SymfonyStyle($input, $output);
      $io-> title("Atempting to upload the registrations");

        $csv = Reader::createFromPath('%kernel.root_dir%/../src/School/GeralBundle/Data/registrations_file.csv');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');
        $records = $csv->getRecords();


        foreach ($records as $row) {
            $aluno = $this->em->getRepository('SchoolAlunoBundle:Aluno')
                ->findOneBy([
                    'id' => $row['student_id'],
                ]);
            if ($aluno === null) {
                continue;
            }

            $matriculaAluno = (new MatriculaAluno())
                ->setAluno($aluno)
                ->setMatricula()

                ;
                    $this->em->persist($matriculaAluno);
        }

//        $this->em->flush();
        $io->success('Registrations imported!');
    }
}
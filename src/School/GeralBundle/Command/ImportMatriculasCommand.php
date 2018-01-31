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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;

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
            ->addArgument('file', InputArgument::REQUIRED, 'The file to input')
            ->setDescription('Imports registers from csv')
            ->setHelp('This command allows you to import registers to the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln('<info>This is the Registrations importer, it depends of the data from of students and courses</info>');
        $output->writeln('<info>Run first "bin/console school:import:students"</info>');
        $output->writeln('<info>and "bin/console school:import:courses"</info>');
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Did  you already the imported students and courses? [enter to continue][CTRL+C to quit]', true);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('<info>OK lets go</info>');
        $io->title("Atempting to upload the registrations");

        $file = $input->getArgument('file');
        if (file_exists($file) && is_file($file) &&  pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';');
            $records = $csv->getRecords();

            foreach ($records as $key => $row) {
                $aluno = $this->em->getRepository('SchoolAlunoBundle:Aluno')
                    ->findOneBy([
                        'idImported' => $row['student_id'],
                    ]);

                $qb = $this->em->createQueryBuilder();
                $qb
                    ->select('m', 'c')
                    ->from('School\MatriculaBundle\Entity\Matricula', 'm')
                    ->leftJoin('m.curso', 'c')
                    ->where('c.idImported = :id')
                    ->setParameter('id', $row['course_id']);

                $matricula = $qb->getQuery()->getSingleResult();

                if (is_null($matricula)) {
                    echo "ddfddf";
                    die();
                    continue;
                }

                $matriculaAluno = (new MatriculaAluno())
                    ->setAluno($aluno)
                    ->setMatricula($matricula);
                $this->em->persist($matriculaAluno);
                if (($key % 25) === 0) {
                    $this->em->flush();
                    $this->em->clear(); // Detaches all objects from Doctrine!
                }
            }

            $this->em->flush(); //Persist objects that did not make up an entire batch
            $this->em->clear();
            $io->success('Registrations imported!');
        }
        else{
            $io->error("No such a csv file");
        }
    }
}
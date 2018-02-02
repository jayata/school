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
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\Container;

class ImportMatriculasCommand extends Command
{
    public function __construct(EntityManagerInterface $em, Container $container)
    {
        parent::__construct();
        $this->em = $em;
        $this->container = $container;
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
        $question = new ConfirmationQuestion('Did  you already imported the students and courses? Yes[ENTER] - No[CTRL+C]', true);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln('<info>OK lets go</info>');
        $io->title("Atempting to upload the registrations");

        $file = $input->getArgument('file');
        if (is_null($file)){
            $appPath = $this->container->getParameter('kernel.root_dir');
            $file= $appPath.'/../src/School/GeralBundle/Data/students_file.csv';
        }
        if (file_exists($file) && is_file($file) && pathinfo($file)['extension'] == 'csv') {

            $csv = Reader::createFromPath($file);
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';');
            $count = $csv->count();
            $records = $csv->getRecords();

            $batchSize = 15;
            $currentSize = 0;
            $progress = new ProgressBar($output, $count);
            $progress->start();
            foreach ($records as $row) {
                $aluno = $this->em->getRepository('SchoolAlunoBundle:Aluno')->findOneBy([
                    'idImported' => $row['student_id'],
                ]);

                if (is_null($aluno)) {
                    $progress->advance();
                    continue;
                }

                try {
                    $qb = $this->em->createQueryBuilder();
                    $qb
                        ->select('m', 'c')
                        ->from('School\MatriculaBundle\Entity\Matricula', 'm')
                        ->leftJoin('m.curso', 'c')
                        ->where('c.idImported = :id')
                        ->setParameter('id', $row['course_id']);

                    $matricula = $qb->getQuery()->getSingleResult();
                } catch (NoResultException $exception) {
                    $progress->advance();
                    continue;
                }

                try {
                    $matriculaAluno = (new MatriculaAluno())
                        ->setAluno($aluno)
                        ->setMatricula($matricula);
                    $this->em->persist($matriculaAluno);
                    $currentSize++;
                    if (($currentSize % $batchSize) === 0) {
                        $this->em->flush();
                    }
                } catch (UniqueConstraintViolationException $exception) {
                    $this->checkEm();
                    continue;
                }
                $progress->advance();
            }
                try {
                    $this->checkEm();
                    $this->em->flush();
                    $this->em->clear();
                } catch (UniqueConstraintViolationException $exception) {}

            $progress->finish();
            $io->newLine();
            $io->success('Registrations imported!');
        } else {
            $io->error("No such a csv file");
        }
    }
    public function checkEm()
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(), $this->em->getConfiguration());
        }
    }
}
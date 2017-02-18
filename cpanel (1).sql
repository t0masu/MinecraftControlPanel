-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2017 at 11:34 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `hosts`
--

CREATE TABLE `hosts` (
  `hostId` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `hostName` varchar(250) NOT NULL,
  `hostAddress` varchar(255) NOT NULL,
  `hostProtocol` enum('SSH','Telnet') NOT NULL,
  `hostUsername` varchar(250) NOT NULL,
  `hostPassword` varchar(250) NOT NULL,
  `hostPort` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hosts`
--

INSERT INTO `hosts` (`hostId`, `ownerId`, `hostName`, `hostAddress`, `hostProtocol`, `hostUsername`, `hostPassword`, `hostPort`) VALUES
(19, 1, 'Testing Host', '172.16.0.104', 'SSH', 'root', '76$ns438', 22);

-- --------------------------------------------------------

--
-- Table structure for table `jarrepo`
--

CREATE TABLE `jarrepo` (
  `jarId` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `path` text NOT NULL,
  `size` int(255) NOT NULL,
  `dateUploaded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jarrepo`
--

INSERT INTO `jarrepo` (`jarId`, `ownerId`, `path`, `size`, `dateUploaded`) VALUES
(1, 1, '/home/user/runpath/my.jar', 112, '2016-12-30 19:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `serverId` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `serverName` varchar(250) NOT NULL,
  `serverHost` varchar(255) NOT NULL,
  `serverPort` varchar(6) NOT NULL,
  `serverVersion` varchar(100) NOT NULL,
  `serverRAM` varchar(250) NOT NULL,
  `serverPath` text NOT NULL,
  `sshUser` varchar(250) NOT NULL,
  `sshPass` text NOT NULL,
  `sshPort` int(6) NOT NULL,
  `jarName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(250) NOT NULL,
  `lastName` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` text NOT NULL,
  `minecraftUsername` varchar(255) DEFAULT NULL,
  `status` enum('0','1','2','3') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `firstName`, `lastName`, `username`, `password`, `minecraftUsername`, `status`) VALUES
(1, 'Admin', 'User', 'admin', '$2y$10$aeSMuGPPUuXLX8iuuAwP/OYrst54u4RMg4ZpSEgm4X41pzpO5uHk6', 'tommy_man5667', '3'),
(2, 'Tom', 'Bellis', 'tom', '$2y$10$hF8IqE/bGfhVM9kJTRy.huPMsw1bm5N8SjSkivBsoxtbKGUTwDUTW', '', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hosts`
--
ALTER TABLE `hosts`
  ADD PRIMARY KEY (`hostId`);

--
-- Indexes for table `jarrepo`
--
ALTER TABLE `jarrepo`
  ADD PRIMARY KEY (`jarId`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`serverId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hosts`
--
ALTER TABLE `hosts`
  MODIFY `hostId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `jarrepo`
--
ALTER TABLE `jarrepo`
  MODIFY `jarId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `serverId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

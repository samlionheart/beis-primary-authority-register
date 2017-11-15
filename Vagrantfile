$script = <<SCRIPT
echo I am provisioning...
date > /etc/vagrant_provision_start


date > /etc/vagrant_provision_end
echo I am provisioned.
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "private_network", ip: "192.168.80.70"

  config.vm.synced_folder ".", "/vagrant", type: "nfs"
  config.vm.synced_folder ".", "/var/www/html", type: "nfs"
  config.vm.network "forwarded_port", guest: 80, host: 8524
  config.vm.provision "shell", inline: $script
  config.vm.provision :shell, :inline => "sudo rm /etc/localtime && sudo ln -s /usr/share/zoneinfo/Europe/London /etc/localtime", run: "always"

  config.vm.provider "virtualbox" do |v|
    v.memory = 3096
    v.cpus = 4
  end
end

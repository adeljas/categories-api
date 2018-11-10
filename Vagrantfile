Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vbguest.auto_update = false
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.provision :shell, :path => "bootstrap.sh"
  config.vm.provider "virtualbox" do |v|
	v.memory = 1024
	v.cpus = 1
  end
end

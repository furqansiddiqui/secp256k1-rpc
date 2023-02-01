# Secp256k1 lib for PHP

* There are hundreds if not 1000+ `secp256k1` implementations available spread across various programming languages.
* The most prominent implementation of `secp256k1` is bitcoin's
  original [libsecp256k1](https://github.com/bitcoin-core/secp256k1/) which is written in C lang.
* **This project aims to provide the ability to work with
  original [libsecp256k1](https://github.com/bitcoin-core/secp256k1/) directly using RPC for projects that are not
  written in C lang.**
* This project runs in a docker container (essentially as a background process) which exposes a TCP port "locally" for
  communications.
* Default configuration prevents any exposure to LAN/WAN.

### Trust, but verify!

Everyone from GitHub community is welcome to audit the codebase.

## :radioactive: Security Considerations

* It is quite actually the same as running Bitcoin core. So, if you have Bitcoin core installed on your server/computer,
  you will notice that all communication between `bitcoind` and its `cli` and `qt` components are in fact made using RPC
  over TCP. Any command sent to `bitcoin-cli` is actually converted to an HTTP call to `bitcoind` RPC server.
* Intended purpose of this project is to **run locally** in your system. It is **NOT** recommended to deploy it
  elsewhere and connect to it remotely. It is even highly discouraged to use it over LAN/vLAN (however any vulnerability
  depends on your use-case and threat model).

## Before Starting

* This project runs inside a docker container. By default, all docker containers share a "bridge" network. So before
  starting, either:
    * Depending on your use case, include this in your existing network of other containers (i.e. Docker Compose) so
      only these containers will be able to communicate with this project, OR:
    * Create a new isolated bridge network just for this container, AND/OR:
    * Expose RPC server port `27271` but bind it with localhost (`-p 127.0.0.1:6000:27271`) so no outside connections
      can approach this container, apart from local ones accessible on port `6000` (example).

## Use Cases

* Educational purposes
* Test vectors / cross-examinations

## Building

To pull the latest release from Docker Hub repo:

`docker pull furqansiddiqui/secp256k1-rpc:latest`

OR, to build from source code:

`docker build -t furqansiddiqui/secp256k1-rpc .`

## Start JSON RPC server

Actual command to run the container should vary according to your own implementation and use case. An example command
that exposes port `27270` locally, while container is given with an isolated docker network:

### Creating a separate docker network:

`docker network create -d bridge secp256k1-isolated-network`

### Run the Secp256k1-RPC server:

`docker run -d -p 127.0.0.1:27270:27271 --network secp256k1-isolated-network --name secp256k1-rpc furqansiddiqui/secp256k1-rpc`



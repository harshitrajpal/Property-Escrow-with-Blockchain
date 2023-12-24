// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

interface IERC20 {
    function totalSupply() external view returns (uint256);
    function balanceOf(address account) external view returns (uint256);
    function transfer(address recipient, uint256 amount) external returns (bool);
    function mint(address to, uint256 amount) external;

    event Transfer(address indexed from, address indexed to, uint256 value);
}

contract NYUDToken is IERC20 {
    string public constant name = "NYUDToken";
    string public constant symbol = "NYUD";
    uint8 public constant decimals = 18;

    uint256 private _totalSupply;
    mapping(address => uint256) private _balances;

    function totalSupply() public view override returns (uint256) {
        return _totalSupply;
    }

    function balanceOf(address account) public view override returns (uint256) {
        return _balances[account];
    }

    function transfer(address recipient, uint256 amount) public override returns (bool) {
        require(_balances[msg.sender] >= amount, "Insufficient balance");
        _balances[msg.sender] -= amount;
        _balances[recipient] += amount;
        emit Transfer(msg.sender, recipient, amount);
        return true;
    }

    function mint(address to, uint256 amount) public override {
        _totalSupply += amount;
        _balances[to] += amount;
        emit Transfer(address(0), to, amount);
    }

    function internalTransfer(address from, address to, uint256 amount) internal {
        require(_balances[from] >= amount, "Insufficient balance");
        _balances[from] -= amount;
        _balances[to] += amount;
        emit Transfer(from, to, amount);
    }
}

contract PropertyRegistry is NYUDToken {
    mapping(string => address) public propertyOwners;
    mapping(string => uint256) public propertyPrices;
    mapping(address => uint256) public escrowDeposits;
    mapping(address => uint256) public escrowTimestamps;
    mapping(address => string) public escrowPropertyHashes;

    function storePropertyHash(string memory propertyHash, uint256 price) public {
        require(propertyOwners[propertyHash] == address(0), "Hash already registered");
        propertyOwners[propertyHash] = msg.sender;
        propertyPrices[propertyHash] = price;
    }

    function verifyPropertyOwner(string memory propertyHash) public view returns (address) {
        return propertyOwners[propertyHash];
    }

    function mintTokens() public {
        mint(msg.sender, 1_000_000 * (10 ** uint256(decimals))); // Mint 1 million NYUD tokens
    }

    function sendToEscrow(string memory propertyHash) public {
        uint256 price = propertyPrices[propertyHash];
        require(price > 0, "Property not for sale");
        require(balanceOf(msg.sender) >= price, "Insufficient token balance");
        internalTransfer(msg.sender, address(this), price);
        escrowDeposits[msg.sender] = price;
        escrowTimestamps[msg.sender] = block.timestamp;
        escrowPropertyHashes[msg.sender] = propertyHash;
    }

    function releaseEscrow() public {
        require(escrowDeposits[msg.sender] > 0, "No escrow deposit found");
        require(block.timestamp >= escrowTimestamps[msg.sender] + 1 minutes, "Escrow period not yet elapsed");

        address originalOwner = propertyOwners[escrowPropertyHashes[msg.sender]];
        uint256 depositAmount = escrowDeposits[msg.sender];

        // Transfer property ownership
        propertyOwners[escrowPropertyHashes[msg.sender]] = msg.sender;

        // Clear escrow records
        escrowDeposits[msg.sender] = 0;
        escrowTimestamps[msg.sender] = 0;
        escrowPropertyHashes[msg.sender] = "";

        // Transfer tokens from escrow to the original property owner
        internalTransfer(address(this), originalOwner, depositAmount);
    }
}

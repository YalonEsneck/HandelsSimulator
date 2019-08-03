package server;

import java.util.HashMap;
import java.util.Map;
import java.util.Scanner;

import trade.Market;
import trade.StockItem;
import utils.UniqueArrayList;

public class Server {

	private static Thread cityMarketWorker;
	private static Market cityMarket;
	private static UniqueArrayList<StockItem> commodities;

	public static void main(String[] args) {
		System.out.println("Welcome to HandelsSimulator!");

		Server.cityMarket = new Market();
		Server.cityMarketWorker = new Thread(new MarketWorker(Server.cityMarket));
		Server.cityMarketWorker.setDaemon(true);
		Server.cityMarketWorker.setName("cityMarket");

		Server.commodities = new UniqueArrayList<StockItem>();

		Scanner scanner = new Scanner(System.in);
		Server.menu(scanner);
		scanner.close();
	}

	private static void generateDummyData() {
		Server.commodities.add(new StockItem("Wheat", 30));
		Server.commodities.add(new StockItem("Flour", 50));
		Server.commodities.add(new StockItem("Bread", 25));

		Server.commodities.add(new StockItem("BeechLog", 200));
		Server.commodities.add(new StockItem("BeechPlank", 500));

		for (StockItem stockItem : Server.commodities) {
			System.out.println(
					"Dummy commodity generated: " + stockItem.getName() + " (" + stockItem.getCurrentPrice() + ")");
		}
	}

	private static void addCommodityToMarket(String name, Integer quantity) {
		for (StockItem stockItem : Server.commodities) {
			if (name.toLowerCase().equals(stockItem.getName().toLowerCase())) {
				synchronized (Server.cityMarket) {
					try {
						Integer newQuantity = Server.cityMarket.addStockItem(stockItem, quantity);
						if (newQuantity < 0) {
							System.out.println(
									"Cannot add commodity " + stockItem.getName() + " as the market is satisfied.");
						} else {
							System.out.println(
									"Added " + stockItem.getName() + " (" + quantity + "). Total: " + newQuantity);
						}
					} catch (Exception e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
						System.out.println("Failed to add " + stockItem.getName() + " (" + quantity + ")!");
					}
				}
				return;
			}
		}
		System.out.println("Failed to add " + name + ": unknown commidity!");
	}

	private static void listCommoditiesInMarket() {
		HashMap<StockItem, Integer> stockItems = Server.cityMarket.getStockItems();
		if (stockItems.size() > 0) {
			for (Map.Entry<StockItem, Integer> stockItem : stockItems.entrySet()) {
				System.out.println(stockItem.getKey().getName() + " (" + stockItem.getValue() + "/"
						+ Server.cityMarket.getStockpileSize() + "): " + stockItem.getKey().getCurrentPrice() + " ("
						+ stockItem.getKey().getMinPrice() + ":" + stockItem.getKey().getStandardPrice() + ":"
						+ stockItem.getKey().getMaxPrice() + ")");
			}
		} else {
			System.out.println("The market is empty.");
		}
	}

	private static void addCommodity(String name, int standardPrice) {
		StockItem newItem = new StockItem(name, standardPrice);
		Server.commodities.add(newItem);
		System.out.println("Added " + newItem.getName() + " (" + standardPrice + ").");
	}

	private static void listCommodities() {
		if (Server.commodities.size() > 0) {
			Server.commodities.forEach(
					(commodity) -> System.out.println(commodity.getName() + " (" + commodity.getStandardPrice() + ")"));
		} else {
			System.out.println("No commodities known.");
		}
	}

	private static void menu(Scanner scanner) {
		System.out.println("Type 'help' to get a list of all commands.");
		String[] command = { "" };

		do {
			System.out.print("$ ");
			String userChoice = scanner.nextLine();
			if (userChoice.length() > 0) {
				command = userChoice.split(" ");
			}
			switch (command[0]) {

			case "help":
			case "?":
				System.out.println("help                                                - print this help message");
				System.out.println("shutdown                                            - stop all workers and exit");
				System.out.println("worker start                                        - start all workers");
				System.out.println("worker stop                                         - stop all workers");
				System.out.println(
						"market list                                         - list all commodities and their prices");
				System.out.println("market add <name<String>> [<quantity<Integer>>]     - add a commodity");
				System.out.println(
						"ware list                                           - list all commodities and their prices");
				System.out.println("ware add <name<String>> <standardPrice<Integer>>    - add a commodity");
				System.out.println("ware dummy                                          - create dummy commodities");
				break;

			case "shutdown":
			case "exit":
			case "quit":
				System.out.print("Okay. Goodbye.");
				return;

			case "worker":
				if (command.length < 2) {
					System.out.println(command[0] + " what?");
					break;
				}
				switch (command[1]) {
				case "start":
					System.out.print("Starting... ");
					Server.cityMarketWorker.start();
					System.out.println("DONE");
					break;

				case "stop":
					if (Server.cityMarketWorker.isAlive()) {
						System.out.print("Stopping... ");
						Server.cityMarketWorker.interrupt();
						try {
							Server.cityMarketWorker.join();
							System.out.println("DONE");
						} catch (InterruptedException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
							System.out.println("FAILED");
						}
					} else {
						System.out.println("Nothing to stop as nothing is running.");
					}
					break;

				default:
					System.out.println("Unknown command: " + command[0] + " " + command[1]);
					break;
				}
				break;

			case "market":
				if (command.length < 2) {
					System.out.println(command[0] + " what?");
					break;
				}
				switch (command[1]) {
				case "add":
					if (command.length < 3 || command.length > 4) {
						System.out.println("Bad syntax! Syntax: " + command[0] + " " + command[1]
								+ " <name<String>> [<quantity<Integer>>]");
					} else {
						String name = command[2];
						Integer quantity = 1;
						if (command.length > 3) {
							quantity = Integer.parseInt(command[3]);
						}

						Server.addCommodityToMarket(name, quantity);
					}
					break;

				case "list":
					Server.listCommoditiesInMarket();
					break;

				default:
					System.out.println("Unknown command: " + command[0] + " " + command[1]);
					break;
				}
				break;

			case "ware":
				if (command.length < 2) {
					System.out.println(command[0] + " what?");
					break;
				}
				switch (command[1]) {
				case "add":
					if (command.length < 4 || command.length > 4) {
						System.out.println("Bad syntax! Syntax: " + command[0] + " " + command[1]
								+ " <name<String>> <standardPrice<Integer>>");
					} else {
						String name = command[2];
						Integer standardPrice = Integer.parseInt(command[3]);
						Server.addCommodity(name, standardPrice);
					}
					break;

				case "list":
					Server.listCommodities();
					break;

				case "dummy":
					Server.generateDummyData();
					break;

				default:
					System.out.println("Unknown command: " + command[0] + " " + command[1]);
					break;
				}
				break;

			default:
				System.out.println("Unknown command: " + command[0]);
				break;
			}
		} while (true);
	}

}
